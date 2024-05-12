<?php
include '../../../config.php';
include '../models/saveAudioModel.php';
include '../models/voiceToTestModel.php';
include '../models/sentenceCompareModel.php';
include '../models/answeringModel.php';
include '../models/aiModel.php';
include '../models/grammarCheckModel.php';
include '../models/spellCheckerModel.php';
include '../models/grammarResultModel.php';
include '../models/pronunciationModel.php';
include '../models/wordSimilarity.php';

$save_audio = new SaveAudioModel($conn);
$voice_to_test = new VoiceToTestModel();
$sentence_compare = new SentenceCompareModel();
$answering = new AnsweringModel($conn);
$ai_model = new AIModel();
$grammer_checker = new GrammarCheckModel();
$spell_checker = new SpellCheckModel();
$grammar_result = new GrammarResultModel($conn);
$pronunciation_result = new PronunciationModel($conn);
$word_similarity = new WordSimilarity($conn);

session_start();
$student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : '';


if($_SERVER["REQUEST_METHOD"] == "POST"){
    if (isset($_POST['task'])) {
        $task = $_POST['task'];
    }

    if($_POST['task'] == 'save_audio'){

        $audio = $_FILES['audio']['tmp_name'];
        $response = $save_audio->save_audio($audio,$student_id);

    }elseif($_POST['task'] == 'normal_scoring'){

        try {
            $Solution = $_POST['Solution'];
            $question_id = $_POST['question_id'];
            $key_words = $_POST['key_words'];
            $test_id = $_POST['test_id'];

            $voice = $voice_to_test->voiceToTest($student_id);
            if(empty($voice)){
                throw new Exception("voice recording or any other input data not received");
            }

            //************** Pronunciation **************/

            $pronun_result = $pronunciation_result -> pronunciationResult($voice, $student_id);

            if(empty($pronun_result)){
                throw new Exception("prnounciation API not working");
            }
            
            $pronun_result_json = json_decode($pronun_result, true);
            $overall_pronun_score = $pronun_result_json['overall_pronun_score'];

            $json_output_result = $pronun_result_json['json_output_string'];


            //************** Overall Fluency **************/

            $confidence_score_json = json_decode($json_output_result , true);
            
            $total_confidence_score = 0;
            foreach ($confidence_score_json as $score_confidence) {
                $total_confidence_score += $score_confidence['confidence'];
            }
            $overall_fluency_score = ($total_confidence_score / 100);


             //************** Content **************/

             $result = $sentence_compare->compareSentences($Solution, $voice);
             if(empty($result)){
                throw new Exception("Some error of compareSentence");
             }

             $serialized_additional_words = serialize($result['additional_words']);
             $serialized_missed_words = serialize($result['missed_words']);
 
             $word_set_1 = implode(', ', $result['additional_words']);
             $word_set_2 = implode(', ', $result['missed_words']);
 
             $count_1 =  count($result['additional_words']);
             $count_2 =  count($result['missed_words']);
 
             $count = ($count_1 + $count_2)/2;
 
             $content = 0;
             if($count > 9){
                 $content = 0;
             }elseif($count >= 7){
                 $content = 1;
             }elseif($count = 6){
                 $content = 2;
             }elseif($count >= 4){
                 $content = 3;
             }elseif($count >= 2){
                 $content = 4;
             }elseif($count >= 0){
                 $content = 5;
             }

            //************** Total Score **************/

            $total = $content + $overall_pronun_score;

            //************** Audio Save **************/

            $uploadDirectory = '../../';
            $audioFile = '../admin/audio/audio-' . date('YmdHis') . '.wav';
            $filename = $uploadDirectory . $audioFile;

            copy('.recording'.$student_id.'.wav', $filename);
            
            //************** Insert the database **************/
            unlink(".recording".$student_id.".wav");

            $response = $answering->Insert($test_id, $voice, $filename, $question_id, $student_id, $content, $overall_pronun_score, NAN, NAN, NAN, $word_set_1, $word_set_1, $json_output_result, NAN, NAN, NAN, $total);

        } catch (Exception $e) {
            unlink(".recording".$student_id.".wav");
            $response['message'] =  "Caught exception: " . $e->getMessage();
        }
        

    }elseif($_POST['task'] == 'advanced_scoring'){
        try {
            $Solution = $_POST['Solution'];
            $question_id = $_POST['question_id'];
            $key_words = $_POST['key_words'];
            $test_id = $_POST['test_id'];


            $voice = $voice_to_test->voiceToTest($student_id);

            if(empty($voice)){
                throw new Exception("voice recording or any other input data not received");
            }

            if($_POST['type'] == 'Answer Short Question'){

                $Question ="Question : `".$key_words."`  and Answer: `".$voice."`  this answer is only give a correct or incorrect not any other";
                $result = $ai_model->AiComparison($Question);

                if(empty($result)){
                    throw new Exception("palm API not working");
                }
    
                if (stripos($result, 'incorrect') !== false) {
                    $value = 0; // Set $value to 1
                } elseif (stripos($result, 'correct') !== false) {
                    $value = 1; // Set $value to 0 if 'incorrect' is found
                }

                //************** Total Score **************/

                $total = $value;

                //************** Audio Save **************/

                $uploadDirectory = '../../';
                $audioFile = '../admin/audio/audio-' . date('YmdHis') . '.wav';
                $filename = $uploadDirectory . $audioFile;

                copy('.recording'.$student_id.'.wav', $filename);
                
                unlink(".recording".$student_id.".wav");
                $response = $answering->Insert($test_id, $voice, $filename, $question_id, $student_id, $value, NAN, NAN, NAN, NAN, NAN, NAN, NAN, NAN, NAN, NAN, $total);
    
            }elseif($_POST['type'] == 'Describe Image' || $_POST['type'] == 'Re-tell Lecture'){

                //************** Content **************/

                $word_similarity_json = $word_similarity->wordSimilarity($voice, $Solution); 

                if(empty($word_similarity_json)){
                    throw new Exception("word similarity API not working");
                }

                $word_similarity_json = json_decode($word_similarity_json, true);
            
                $similarity_score = round($word_similarity_json['similarity']*100);
                if($similarity_score == 100){
                    $content = 5;
                }elseif($similarity_score > 79){
                    $content = 4;
                }elseif($similarity_score > 59){
                    $content = 3;
                }elseif($similarity_score > 39){
                    $content = 2;
                }elseif($similarity_score > 9){
                    $content = 1;
                }else{
                    $content = 0;
                }  
                
                //************** Pronunciation **************/

                $pronun_result = $pronunciation_result -> pronunciationResult($voice, $student_id);
                
                if(empty($pronun_result)){
                    throw new Exception("prnounciation API not working");
                }

                $pronun_result_json = json_decode($pronun_result, true);
        
                $overall_pronun_score = $pronun_result_json['overall_pronun_score'];

                $json_output_result = $pronun_result_json['json_output_string'];
                
                //************** Overall Fluency **************/

                $confidence_score_json = json_decode($json_output_result , true);
                $total_confidence_score = 0;
                foreach ($confidence_score_json as $score_confidence) {
                    $total_confidence_score += $score_confidence['confidence'];
                }
                $overall_fluency_score = ($total_confidence_score / 100);
    
                $json_output_result = addslashes($json_output_result);

                //************** Total Score **************/

                $total = $content + $overall_pronun_score;

                //************** Audio Save **************/

                $uploadDirectory = '../../';
                $audioFile = '../admin/audio/audio-' . date('YmdHis') . '.wav';
                $filename = $uploadDirectory . $audioFile;

                copy('.recording'.$student_id.'.wav', $filename);

                unlink(".recording".$student_id.".wav");
                $response = $answering->Insert($test_id, $voice, $filename, $question_id, $student_id, $content, $overall_pronun_score, NAN, NAN, NAN, NAN, NAN, $json_output_result, NAN, NAN, NAN, $total);
            }


        }catch(Exception $e){
            unlink(".recording".$student_id.".wav");
            $response['message'] =  "Caught exception: " . $e->getMessage();
        }
    }elseif($_POST['task'] == 'writing_marks'){
        
        try{
            //Assigned the variables
            $answer = $_POST['answer'];
            $question_id = $_POST['question_id'];
            $key_words = $_POST['key_words'];
            $test_id = $_POST['test_id'];


            //---------### Form Score ###---------------------

            $sentencesArray = explode('.', $answer);
            $sentencesArray = array_filter($sentencesArray);
            $numberOfSentences = count($sentencesArray);
            $numberOfWords = str_word_count($answer);

            //---------### Grammar Score ###---------------------

            $grammer_json_data = $grammer_checker->grammarChecker($answer);

            if(empty($grammer_json_data)){
                throw new Exception("grammer error checking API not working");
            }

            $data = json_decode($grammer_json_data, true);
            $grammer_error_count = isset($data[0]['result']) ? count($data[0]['result']) : 0;
            
            $newString = str_replace("'", "\'", $answer);
            $Question = "'".$newString."' is this an obstacle to communication or not. not any comments";
            $value = $ai_model->AiComparison($Question);
            if(empty($value)){
                throw new Exception("palm API not working");
            }

            //------------### Vocabulary Score ###---------------------
            $wordCount = str_word_count($answer);
            $Question ="Question : `".$answer."`  how many vocabulary errors only integer count not any answers or suggessions";
            $vocabulary_errors = $ai_model->AiComparison($Question);
            if(empty($vocabulary_errors)){
                throw new Exception("palm API not working");
            }
            $diff = $wordCount - $vocabulary_errors;


            //---------### Spelling Score ###---------------------
            $errorCount = 0;
            foreach ($data as $item) {
                foreach ($item['result'] as $result) {
                    foreach($result['error_category'] as $error){
                        if ($error === 'Spellings & Typos' || $error === 'Spelling & Typos') {
                            $errorCount++; 
                        }
                    }
                }
            }



            if($numberOfWords > 8){
                if($numberOfWords > 23){
                    $content = 2;
                }else{
                    $content = 1;
                }
                if($numberOfSentences == 1 && $numberOfWords > 5 && $numberOfWords < 75){
                    $form = 1;
                    if($grammer_error_count === 0){
                        $grammar = 2;
                    }else if(strpos($value, "no") !== false || strpos($value, "No") !== false) {
                        $grammar = 1;
                    }else if(strpos($value, "Yes") !== false || strpos($value, "yes") !== false){
                        $grammar = 0;
                    }

                    if($errorCount == 0){
                        $spell = 2;
                    }else if($errorCount == 1){
                        $spell = 1;
                    }else{
                        $spell = 0;
                    }

                    if($diff > 23){
                        $vocab = 2;
                    }else if($diff > 7){
                        $vocab = 1;
                    }else{
                        $vocab = 0;
                    }

                }else{
                    $form = 0;
                    $grammar = 0;
                    $spell = 0;
                    $vocab = 0;
                }
            }else{
                $content = 0;
                $grammar = 0;
                $spell = 0;
                $form = 0;
                $vocab = 0;
            }
            $total = $content + $grammar + $form + $vocab;
            //$response['message'] =  $total;

            $response = $answering->Insert($test_id, $newString , NAN, $question_id, $student_id, $content, NAN, $grammar, $vocab, $form, NAN, NAN, NAN, $spell, NAN, NAN, $total);
            $response['grammar'] = $grammar_result->grammerResult($grammer_json_data);

        }catch(Exception $e){
            $response['message'] =  "Caught exception: " . $e->getMessage();
        }


    }else if($_POST['task'] == 'essay_write'){

        try{

            //Assigned the variables
            $answer = $_POST['answer'];
            $question_id = $_POST['question_id'];
            $key_words = $_POST['key_words'];
            $test_id = $_POST['test_id'];


            //---------### Form & Content Score ###---------------------
            $numberOfWords = str_word_count($answer);




            //---------### Grammar Score ###---------------------
            $grammer_json_data = $grammer_checker->grammarChecker($answer);

            if(empty($grammer_json_data)){
                throw new Exception("grammer error checking API not working");
            }

            $data = json_decode($grammer_json_data, true);
            $grammer_error_count = 0;
            foreach ($data as $item) {
                $grammer_error_count += isset($item['result']) ? count($item['result']) : 0;
            }
    
            $newString = str_replace("'", "\'", $answer);
            $Question = "'".$newString."' is this an obstacle to communication or not. not any comments";
            $value = $ai_model->AiComparison($Question);
            if(empty($value)){
                throw new Exception("palm API not working");
            }




            //---------### Spelling Score ###---------------------

            $errorCount = 0;
            foreach ($data as $item) {
                foreach ($item['result'] as $result) {
                    foreach($result['error_category'] as $error){
                        if ($error === 'Spellings & Typos' || $error === 'Spelling & Typos') {
                            $errorCount++; 
                        }
                    }
                }
            }


            //------------### Vocabulary Score ###---------------------
            $wordCount = str_word_count($answer);
            $Question ="Question : `".$answer."`  how many vocabulary errors only integer count not any answers or suggessions";
            $vocabulary_errors = $ai_model->AiComparison($Question);
            if(empty($vocabulary_errors)){
                throw new Exception("palm API not working");
            }
            $diff = $wordCount - $vocabulary_errors;


            if($numberOfWords > 119){

                if($numberOfWords > 279){
                    $content = 3;
                    $dsc = 2;
                    $glr = 2;
                }else if($numberOfWords > 199){
                    $content = 2;
                    $dsc = 1;
                    $glr = 1;
                }else{
                    $content = 1;
                    $dsc = 1;
                    $glr = 1;
                }
    
    
    
    
                if($numberOfWords > 120 && $numberOfWords < 380){
                    if($numberOfWords > 200 && $numberOfWords < 300){
                        $form = 2;
                    }else{
                        $form = 1;
                    }
    
    
                    if($grammer_error_count === 0){
                        $grammar = 2;
                    }else if(strpos($value, "no") !== false || strpos($value, "No") !== false) {
                        $grammar = 1;
                    }else if(strpos($value, "Yes") !== false || strpos($value, "yes") !== false){
                        $grammar = 0;
                    }
    
                    if($errorCount == 0){
                        $vocab = 2;
                    }else if($errorCount == 1){
                        $vocab = 1;
                    }else{
                        $vocab = 0;
                    }
    
                    if($diff > 23){
                        $spell = 2;
                    }else if($diff > 7){
                        $spell = 1;
                    }else{
                        $spell = 0;
                    }
    
    
    
                }else{
                    $form = 0;
                    $grammar = 0;
                    $spell = 0;
                    $dsc = 0;
                    $glr = 0;
                    $vocab = 0;
                }
            }else{
                $content = 0;
                $grammar = 0;
                $spell = 0;
                $vocab = 0;
                $form = 0;
                $dsc = 0;
                $glr = 0;
            }
            
            $total = $content + $grammar + $spell + $vocab + $form + $dsc + $glr;
            
    
            $response = $answering->Insert($test_id, $newString , NAN, $question_id, $student_id, $content, NAN, $grammar, $vocab, $form, NAN, NAN, NAN, $spell, $dsc, $glr, $total);
            $response['grammar'] = $grammar_result->grammerResult($grammer_json_data);    

        }catch(Exception $e){
            $response['message'] =  "Caught exception: " . $e->getMessage();
        }
    }else if($_POST['task'] == 'reading_marks'){
        $answer = $_POST['answer'];
        $question_id = $_POST['question_id'];
        $test_id = $_POST['test_id'];
        $score = $_POST['score'];

        $response = $answering->Insert($test_id, $answer, NAN, $question_id, $student_id, $score, NAN, NAN, NAN, NAN, NAN, NAN, NAN, NAN, NAN, NAN, $score);
    
    }else if($_POST['task'] == 'dictation'){
        $answer = $_POST['answer'];
        $Solution = $_POST['Solution'];
        $question_id = $_POST['question_id'];
        $test_id = $_POST['test_id'];
       
        
        $result = $sentence_compare->compareSentences($Solution, $answer);

        $serialized_additional_words = serialize($result['additional_words']);
        $serialized_missed_words = serialize($result['missed_words']);

        $word_set_1 = implode(', ', $result['additional_words']);
        $word_set_2 = implode(', ', $result['missed_words']);

        $count_1 =  count($result['additional_words']);
        $count_2 =  count($result['missed_words']);

        $solution_word_count = str_word_count($Solution);

        $count = ($solution_word_count - $count_2);

        //$response['message'] = $count_2;
        //$response['message1'] = $word_set_2;

        //$response['message'] = $count;

        $response = $answering->Insert($test_id, $answer, NAN, $question_id, $student_id, $count, NAN, NAN, NAN, NAN, $word_set_1, $word_set_2, NAN, NAN, NAN, NAN, $count);
    
    }else if ($_POST['task'] == 'delete-practice-answer') {
        $answer_id = $_POST['answer_id'];

        try{
            $sql_delete_1 = "DELETE FROM grammar_result WHERE answer_id = $answer_id";
            $sql_delete_2 = "DELETE FROM answering WHERE answer_id = $answer_id";

            
            if ($conn->query($sql_delete_1) === TRUE && $conn->query($sql_delete_2) === TRUE) {
                $response['success'] = true;
                $response['message'] = "data deleted successfully!";
            } else {
                $response['success'] = false;
                $response['message'] = $sql;
            }
            
        }catch (Exception $e) {
            // Handle database connection or query errors here
            $response['success'] = false;
            $response['message'] = "Error: " . $e->getMessage();
        }
        
    }else if($_POST['task'] == 'incomplete_test'){
        $test_id = $_POST['test_id'];
        try{
            $sql_1 = "UPDATE paidtest SET attempted = 2 WHERE test_id = $test_id AND student_id = '$student_id'";

            
            if ($conn->query($sql_1) === TRUE) {
                $response['success'] = true;
                $response['message'] = "data updated successfully!";
            } else {
                $response['success'] = false;
                $response['message'] = $sql_1;
            }
            
        }catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = "Error: " . $e->getMessage();
        }

    }else if($_POST['task'] == 'attempted_data'){
        $test_id = $_POST['test_id'];
        $evaluated = $_POST['evaluated'];
        try{
            $sql_1 = "INSERT INTO `paidtest` (`student_id`, `test_id`, `attempted_on`, `attempted`, `transaction_id`, `evaluation_on`, `evaluated`) VALUES ('$student_id', '$test_id', CURDATE(), '$evaluated', NULL, NULL, 0);";

            
            if ($conn->query($sql_1) === TRUE) {
                $response['success'] = true;
                $response['message'] = "data updated successfully!";
            } else {
                $response['success'] = false;
                $response['message'] = $sql_1;
            }
            
        }catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = "Error: " . $e->getMessage();
        }
    }else if($_POST['task'] == 'complete_test'){
        $test_id = $_POST['test_id'];
        try{
            $sql_1 = "UPDATE paidtest SET attempted = 1 WHERE test_id = $test_id AND student_id = '$student_id'";

            
            if ($conn->query($sql_1) === TRUE) {
                $response['success'] = true;
                $response['message'] = "data updated successfully!";
            } else {
                $response['success'] = false;
                $response['message'] = $sql_1;
            }
            
        }catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = "Error: " . $e->getMessage();
        }

    }

}

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>