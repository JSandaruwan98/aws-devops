<?php

class Answer {
    public $question_id;
    public $student_id;
    public $test_id;
    public $audio_file;
    public $user_answer;
    public $content;
    public $pronunciation;
    public $oral_fluency;
    public $grammer;
    public $vocabulary;
    public $form;
    public $additional_words;
    public $missed_words;
    public $json_data;

    public function __construct($question_id, $student_id, $test_id, $audio_file, $user_answer, $content, $pronunciation, $oral_fluency, $grammer, $vocabulary, $form, $additional_words, $missed_words, $json_data) {
        $this->question_id = $question_id;
        $this->student_id = $student_id;
        $this->test_id = $test_id;
        $this->audio_file = $audio_file;
        $this->user_answer = $user_answer;
        $this->content = $content;
        $this->pronunciation = $pronunciation;
        $this->oral_fluency = $oral_fluency;
        $this->grammer = $grammer;
        $this->vocabulary = $vocabulary;
        $this->form = $form;
        $this->additional_words = $additional_words;
        $this->missed_words = $missed_words;
        $this->json_data = $json_data;
    }

    // Getter method for answer_id
    public function get_question_id() {
        return $this->question_id;
    }

    public function get_student_id(){
        return $this->student_id;
    }

    public function get_audio_file(){
        return $this->audio_file;
    }

    public function get_user_answer(){
        return $this->user_answer;
    }

    public function get_content() {
        return $this->content;
    }

    public function get_pronunciation() {
        return $this->pronunciation;
    }

    public function get_oral_fluency(){
        return $this->oral_fluency;
    }

    public function get_grammer(){
        return $this->grammer;
    }

    public function get_vocabulary(){
        return $this->vocabulary;
    }

    public function get_form(){
        return $this->form;
    }

    public function get_additional_words(){
        return $this->additional_words;
    }

    public function get_missed_words(){
        return $this->missed_words;
    }

    public function get_json_words(){
        return $this->json_data;
    }
}


?>
