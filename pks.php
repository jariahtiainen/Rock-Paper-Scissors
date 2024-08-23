
<?php
/*
$pks=["paperi","kivi","sakset"];
$randomnum=rand(0,1);
$tulos=$pks[$randomnum];
echo("Tietokone valitsi: ". $tulos);
*/
?>

<?php
    // Start PHP session
    session_start(); 

    // Function to get the computer's choice
    function getComputerChoice() {
        $choices = array("rock", "paper", "scissors");
        $randomIndex = array_rand($choices);
        return $choices[$randomIndex];
    }

    // Function to determine the winner
    function determineWinner($playerChoice, $computerChoice) {
        if ($playerChoice === $computerChoice) {
            return "It's a tie!";
        } elseif (
            ($playerChoice === "rock" && $computerChoice === "scissors") ||
            ($playerChoice === "paper" && $computerChoice === "rock") ||
            ($playerChoice === "scissors" && $computerChoice === "paper")
        ) {
            return "You win!";
        } else {
            return "Computer wins!";
        }
    }

    // Get player's choice from clicked image
    $playerChoice = isset($_GET['choice']) ? $_GET['choice'] : null;

    // Get computer's choice
    $computerChoice = getComputerChoice();

    // Determine the winner if player has made a choice
    $result = "";
    if ($playerChoice) {
        $result = determineWinner($playerChoice, $computerChoice);
        if ($result === "You win!") {
            // Increment streak if player wins
            $_SESSION['streak'] = isset($_SESSION['streak']) ? $_SESSION['streak'] + 1 : 1;
            // Update highest streak if current streak is higher
            if (!isset($_SESSION['highestStreak']) || $_SESSION['streak'] > $_SESSION['highestStreak']) {
                $_SESSION['highestStreak'] = $_SESSION['streak'];
            }
        } else {
            // Reset streak if player loses or ties
            $_SESSION['streak'] = 0;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rock Paper Scissors</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100vh;   /* 100% of viewport height*/
            background-color: teal;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0px;
            padding: 0px;
            overflow: hidden;  /* hides scrollbars */
        }
        canvas {
            display: flex;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100vh;
            text-align: center;
            z-index: 1;
            opacity: 100%;
 
        }
        .emojis {
            display: flex;
            justify-content: space-between;     /* align items in flexbox horizontally */
            align-items: normal;                /* align items in flexbox vertically */
            top: 530px;                /* Adjust the top position as needed */
            left: center;
        }
        .emoji {
            font-size: 4em;
            margin: 10px 0;
            cursor: pointer;
            text-decoration: none; /* Remove underlining */
        }
        h2 {
            transition: transform 3.3s ease; /* Smooth transition for rotation */
            top: 250px;                
            left: center;
        }
        h2:hover {
            transform: rotate(180deg); /* Rotate the text element # degrees on hover */
        }
        #highest_streak {
            display: flex;
            
            top: 0;
            left: 100;
        }
        .item {
            margin: 10px 0;
        }
        
        /*Animations*/    
            @keyframes jitter {
            0% { transform: translateX(-2px); }
            25% { transform: translateX(2px); }
            50% { transform: translateX(-2px); }
            75% { transform: translateX(2px); }
            100% { transform: translateX(0); }
            }
            @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
            }
            @keyframes wave {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(20deg); }
            }
            #rock:hover {
                animation: pulse 1s infinite; 
            }
            #paper:hover { 
                transform: rotateY(180deg);
            }
            #scissors:hover {
                animation: wave 0.4s ease infinite;
            }

        /* CSS rules for screens with max-width 600 pixels. */
            @media only screen and (max-width: 600px) {         /* "only screens" means apply only to screen media type, excluding other media types like print or speech. */
                html, body {
                display: flex;
                justify-content: center;
                height: 90vh;   /* % of viewport height*/
                background-color: pink;
                color: white;
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                overflow: hidden;  /* hides scrollbars */
                }
                .container {
                    height: auto;   /* Reset height to auto to allow content to expand vertically */
                }
                .emojis {
                    display: flex;
                    justify-content: space-between;     /* align items in flexbox horizontally */
                    align-items: normal;                /* align items in flexbox vertically */
                    top: 330px;                         /* Adjust the top position as needed */
                    left: center;                                            
                }
                h2 {
                    transition: transform 3.3s ease;    /* Smooth transition for rotation */
                    top: 100px;                
                    left: center;
                }
            }   /* CSS rules for smaller screens END*/
    </style>
</head>
<body>
    <div class="container">
        <canvas id="canvas"></canvas>
        <h2 class="item">Rock, Paper, Scissors</h2>
        <?php if (!$playerChoice): ?>
        <?php else: ?>
          <p class="item">Your choice: <?php echo ucfirst($playerChoice); ?></p>
          <p class="item">Computer's choice: <?php echo ucfirst($computerChoice); ?></p>
          <h3 class="item"><?php echo $result; ?></h3>
        <?php endif; ?>
        <div class="item emojis">
          <a href="?choice=rock" class="emoji" id="rock">👊</a>
          <a href="?choice=paper" onmouseover="rotateYDIV(this)" class="emoji" id="paper">✋</a>
          <a href="?choice=scissors" class="emoji" id="scissors">✌️</a>
        </div>
        <p class="item">Streak: <?php echo isset($_SESSION['streak']) ? $_SESSION['streak'] : 0; ?></p>
        <p id="highest_streak">Highest streak: <?php echo isset($_SESSION['highestStreak']) ? $_SESSION['highestStreak'] : 0; ?></p>
            
    </div>
    

    <script type="text/javascript">
    
    // 3D rotate code from https://www.w3schools.com/css/css3_3dtransforms.asp
    var x, y, n = 0, ny = 0, rotINT, rotYINT;

    function rotateDIV() {
        x = document.getElementById("test");
        clearInterval(rotINT);
        rotINT = setInterval("startRotate()", 10);
    }

    function rotateYDIV() {
        y = document.getElementById("rotate3D");
        clearInterval(rotYINT);
        rotYINT = setInterval("startYRotate()", 10);
    }

    function startRotate() {
        n = n + 1;
        x.style.transform = "rotate(" + n + "deg)";
        x.style.webkitTransform = "rotate(" + n + "deg)";
        x.style.OTransform = "rotate(" + n + "deg)";
        x.style.MozTransform = "rotate(" + n + "deg)";
        if (n == 180 || n == 360) {
            clearInterval(rotINT);
            if (n == 360) {
                n = 0;
            }
        }
    }

    function startYRotate() {
        ny = ny + 1;
        y.style.transform = "rotateY(" + ny + "deg)";
        y.style.webkitTransform = "rotateY(" + ny + "deg)";
        y.style.OTransform = "rotateY(" + ny + "deg)";
        y.style.MozTransform = "rotateY(" + ny + "deg)";
        if (ny == 180 || ny >= 360) {
            clearInterval(rotYINT);
            if (ny >= 360) {
                ny = 0;
            }
        }
    }
    //3D rotate code END

    // PHP variable for the result
    var result = "<?php echo $result; ?>";

        // JavaScript code for the confetti effect
        if (result === "You win!") {
            var winSound = new Audio('audio/cheer.wav');
            winSound.play();
            
            canvas = document.getElementById("canvas");
            ctx = canvas.getContext("2d");
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            cx = ctx.canvas.width / 2;
            cy = ctx.canvas.height / 2;

            let confetti = [];
            const confettiCount = 2000;
            const gravity = 0.9;
            const terminalVelocity = 1;
            const drag = 0.095;
            const colors = [
                { front: 'red', back: 'darkred' },
                { front: 'green', back: 'darkgreen' },
                { front: 'blue', back: 'darkblue' },
                { front: 'yellow', back: 'darkyellow' },
                { front: 'orange', back: 'darkorange' },
                { front: 'pink', back: 'darkpink' },
                { front: 'purple', back: 'darkpurple' },
                { front: 'turquoise', back: 'darkturquoise' }
            ];

            resizeCanvas = () => {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
                cx = ctx.canvas.width / 2;
                cy = ctx.canvas.height / 2;
            };

            // Function to generate random number within a range
            randomRange = (min, max) => Math.random() * (max - min) + min;

            // Function to initialize confetti
            initConfetti = () => {
                for (let i = 0; i < confettiCount; i++) {
                    confetti.push({
                        color: colors[Math.floor(randomRange(0, colors.length))],
                        dimensions: {
                            x: randomRange(10, 20),
                            y: randomRange(10, 30)
                        },
                        position: {
                            x: randomRange(0, canvas.width),
                            y: canvas.height - 1
                        },
                        rotation: randomRange(0, 2 * Math.PI),
                        scale: {
                            x: randomRange(0.6, 1),
                            y: randomRange(0.6, 1)
                        },
                        velocity: {
                            x: randomRange(-25, 25),
                            y: randomRange(0, -50)
                        }
                    });
                }
            };

            // Function to render confetti
            render = () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                confetti.forEach((confetto, index) => {
                    let width = confetto.dimensions.x * confetto.scale.x;
                    let height = confetto.dimensions.y * confetto.scale.y;

                    // Move canvas to position and rotate
                    ctx.translate(confetto.position.x, confetto.position.y);
                    ctx.rotate(confetto.rotation);

                    // Apply forces to velocity
                    confetto.velocity.x -= confetto.velocity.x * drag;
                    confetto.velocity.y = Math.min(confetto.velocity.y + gravity, terminalVelocity);
                    confetto.velocity.x += Math.random() > 0.5 ? Math.random() : -Math.random();

                    // Set position
                    confetto.position.x += confetto.velocity.x;
                    confetto.position.y += confetto.velocity.y;

                    // Delete confetti when out of frame
                    if (confetto.position.y >= canvas.height) confetti.splice(index, 1);

                    // Loop confetto x position
                    if (confetto.position.x > canvas.width) confetto.position.x = 0;
                    if (confetto.position.x < 0) confetto.position.x = canvas.width;

                    // Spin confetto by scaling y
                    confetto.scale.y = Math.cos(confetto.position.y * 0.1);
                    ctx.fillStyle = confetto.scale.y > 0 ? confetto.color.front : confetto.color.back;

                    // Draw confetti
                    ctx.fillRect(-width / 2, -height / 2, width, height);

                    // Reset transform matrix
                    ctx.setTransform(1, 0, 0, 1, 0, 0);
                });

                // Fire off another round of confetti (infinitely)
             /* if (confetti.length <= 10) initConfetti(); */

                window.requestAnimationFrame(render);
            };

            // Initialize confetti and start rendering
            initConfetti();
            render();

            // Resize canvas when the window is resized
            window.addEventListener('resize', function () {
                resizeCanvas();
            });
        }
    </script>
</body>
</html>






