<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Dice Roller</title>

	    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
		<link rel="stylesheet" type="text/css" href=" {{ asset( 'css/dice.css' ) }} " />
		
	</head>
	<body>
    @if($haveChange)
	<div class="container list-inline">
        <a class="backRegister" href="{{ route( 'web._logout' ) }}"><button class="btn_logout"> < </button></a>
        <div id="setting_dice" class="fixed-bottom">
            <button class="dice_btn" id="roll_btn"> roll </button>
        </div>
        <div class="div_dise">
            <div class="cube1 cube list-inline-item" id="cube">
                <div class="front">
                </div>
                <div class="back">
                </div>
                <div class="top">
                </div>
                <div class="left">
                </div>
                <div class="right">
                </div>
                <div class="bottom">
                </div>
            </div>
            <div class="cube2 cube list-inline-item" id="cube">
            </div>
            <div class="cube3 cube list-inline-item" id="cube">
            </div>
            <div class="cube4 cube list-inline-item" id="cube">
            </div>
            <div class="cube5 cube list-inline-item" id="cube">
            </div>
        </div>
	</div>
    @else
    <script>
        window.location.href = '{{ route('web._logout') }}';
    </script>
    @endif
    
	<script>
        $(document).ready(function() {

            var diceFacesHTML = $('.cube1').html();
            for(let i= 2; i<= 5; i++){
                $('.cube' + i).html(diceFacesHTML.repeat(1));
                $('.cube' + i).hide();
            }
            
            diceNum();

            $('#roll_btn').click(function(){
                getDiceResult();
                $(' #setting_dice ').hide();
            });

            function getDiceResult(){

                $.ajax({
                    url: ' {{ route('web.getDiceResult') }} ',
                    method: 'GET',
                    success: function( response ){
                        updateRotation(response.data);
                    },
                    error: function( error ){
                        console.log( error );
                    }
                });

            }

            function diceNum(){
                $.ajax({
                    url: ' {{ route('web.getDiceNumber') }} ',
                    method: 'GET',
                    success: function( response ){
                        var numDice = response.data;
                        var diceFacesHTML = $('.cube1').html();
                        
                        for(let i= 2; i<= numDice; i++){
                            $('.cube' + i).show();
                        }
                    },
                    error: function( error ){
                        console.log( error );
                    }
                });
            }

            function updateRotation(result) {
                angleArray = [[0,0,0],[-310,-362,-38],[-400,-320,-2],[135,-217,-88],[-224,-317,5],[-47,-219,-81],[-133,-360,-53]];
                               
                $.each( result, function( index, value ) {
                    cubeIndex = index + 1;
                    $('.cube' + cubeIndex).css('transform', 'rotateX('+angleArray[value][0]+'deg) rotateY('+angleArray[value][1]+'deg) rotateZ('+angleArray[value][2]+'deg)');
                    console.log(value);
                });

                angleArray = [[0,0,0],[-310,-362,-38],[-400,-320,-2],[135,-217,-88],[-224,-317,5],[-47,-219,-81],[-133,-360,-53]];

                $.each(result, function(index, value) {
                    cubeIndex = index + 1;
                    var rotation = angleArray[value];
                    var animationName = 'rotateAnimation' + cubeIndex; 
                    
                    var keyframes = '@keyframes ' + animationName + ' {\
                                        50% { transform: rotateX(145deg) rotateY(165deg) rotateZ(135deg); top: 20%; }\
                                        100% { transform: rotateX(' + rotation[0] + 'deg) rotateY(' + rotation[1] + 'deg) rotateZ(' + rotation[2] + 'deg); top: 40%; }\
                                    }';
                    
                    $('head').append('<style>' + keyframes + '</style>');
                    
                    $('.cube' + cubeIndex).css({
                        'animation-name': animationName,
                        'animation-duration': '2s', 
                        'animation-timing-function': 'linear'
                    });
                });



            }

        });

    </script>
    
	</body>
</html>