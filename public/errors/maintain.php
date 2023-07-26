<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    <?php
        function check($b) {
            return (0 == strcmp($_GET['status'], $b));
        }
        if(check('404')){
            echo 'Error '.$_GET['status'].' Not Found';
        }else if(check('403')){
            echo 'Error '.$_GET['status'].' Forbidden';
        }else if(check('503')){
            echo 'Error '.$_GET['status'].' Service Temporarily Unavailable';
        }else if(check('500')){
            echo 'Error '.$_GET['status'].' Internal Server Error';
        }else{
            echo 'Error '.$_GET['status'].' Server Error';
        }
    ?>
    </title>
</head>
<body>

  <div id="main">
    <div class="fof">
        <h1>
        <?php
            if(check('404')){
                echo 'Error '.$_GET['status'].'. Not Found';
            }else if(check('403')){
                echo 'Error '.$_GET['status'].'. Forbidden';
            }else if(check('503')){
                echo 'Error '.$_GET['status'].'. Service Temporarily Unavailable';
            }else if(check('500')){
                echo 'Error '.$_GET['status'].'. Internal Server Error';
            }else{
                echo 'Error '.$_GET['status'].'. Server Error';
            }
        ?>
        </h1>
    </div>
</div>

</body>
</html>


<style>
*{
    transition: all 0.6s;
}

html {
    height: 100%;
}

body{
    font-family: 'Lato', sans-serif;
    color: #888;
    margin: 0;
}

#main{
    display: table;
    width: 100%;
    height: 100vh;
    text-align: center;
}

.fof{
	  display: table-cell;
	  vertical-align: middle;
}

.fof h1{
	  font-size: large;
	  display: inline-block;
	  padding-right: 12px;
	  animation: type .5s alternate infinite;
}

@keyframes type{
	  from{box-shadow: inset -3px 0px 0px #888;}
	  to{box-shadow: inset -3px 0px 0px transparent;}
}
</style>


