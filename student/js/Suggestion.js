$(document).ready(function() {
    $('.togel-section1').show()
    $('#total').on("click", function(){
        $('.togel-section1').show()
        $('.togel-section2').hide()
        $('#content, #pronoun, #fluency').removeClass('blue-background');
        $('#content-score, #pronoun-score, #fluency-score').removeClass('white-text');
    })
    $('#content').on("click", function(){
        $('.togel-section2').show()
        $('.togel-section1').hide()
        $('#content-score').addClass('white-text');
        $('#content').addClass('blue-background');
        $('#pronoun, #fluency').removeClass('blue-background');
        $('#pronoun-score, #fluency-score').removeClass('white-text');
        $('#suggestion-head').text(content)
    })
    $('#pronoun').on("click", function(){
        $('.togel-section2').show()
        $('.togel-section1').hide()
        $('#pronoun-score').addClass('white-text');
        $('#pronoun').addClass('blue-background');
        $('#content, #fluency').removeClass('blue-background');
        $('#content-score, #fluency-score').removeClass('white-text');
    })
})