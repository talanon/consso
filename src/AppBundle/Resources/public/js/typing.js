/**
 * Created by Leo-MEYER on 19/04/2017.
 */
var text = "";
var count = 0;
var goal = 0;
var maxspeed = 200;
var ended = false;

$(document).ready(function() {


    //$('.wrapper').hide();
    $('.consso_mainPage_text').hide();
    $('.consso_mainPage').hide();

    function typeit(tweet) {
        text = tweet+"                               ";
        type();
    }

    function character(start, end, text) {
        return text.substring(start, end);
    }

    function type() {
        var random = Math.floor(Math.random() * maxspeed);
        setTimeout(type, random);
        $('.consso_mainPage_text').append(character(count, count+1, text));
        count++;
        if(count==text.length){
            goal++;
            ended=true;
        }
        if(ended==true && goal < 5){
            ended=false;
            count = 0;
            $('.consso_mainPage_text').html('');
            switch(goal){
                case 1:
                    typeit(" Si tu souhaites en apprendre d'avantage sur mon concepteur.");
                    break;
                case 2:
                    typeit(" Ou sur l'objectif du site, clique sur moi.");
                    break;
                case 3:
                    typeit(" Bonne visite.");
                    break;
                case 4 :
                    $('.consso_mainPage_text').fadeOut();
                    $('.consso_mainPage').fadeOut();
                    $('.wrapper').fadeIn();
                    break;
            }
        }
    }

    type();
    typeit(" Hello, je m'appelle consso. Je suis ici pour t'éclairer sur ta consommation personnelle !");




});