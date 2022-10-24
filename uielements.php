<?php

    function showNavigationBar($session) {
        $returnString = '<nav class="navbar navbar-expand-lg navbar-light bg-light">';
        $returnString .=    '<a class="navbar-brand" href="index.php">Croaker</a>';
        $returnString .=    '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">';
        $returnString .=        '<span class="navbar-toggler-icon"></span>';
        $returnString .=    '</button>';
        $returnString .=    '<div class="collapse navbar-collapse" id="navbarSupportedContent">';
        $returnString .=        '<ul class="navbar-nav mr-auto">';
                                if (!isset($session['loggedin']) or !$session['loggedin']) {
                                    //wenn Session nicht personalisiert - Link zu Login / Register anzeigen
                                    $returnString .= '<li class="nav-item"><a class="nav-link" href="register.php">Registrierung</a></li>';
                                    $returnString .= '<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>';
                                //die();
                                } else {
                                    //wenn Session personalisiert ist - Link zu Logout anzeigen
                                    $returnString .= '<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>';
                                    $returnString .= '<li class="nav-item"><a class="nav-link" href="options.php">Optionen</a></li>';
                                    $returnString .= '<li class="nav-item"><a class="nav-link" href="myspace.php"><div style="float: right;"><img src="croaker.png" width=40></div></a></li>';
                                }
        $returnString .=        '</ul>';           
        $returnString .=    '</div>';
        $returnString .='</nav>';

        return $returnString;
    }

    function showPostButton($session) {
        if (!isset($session['loggedin']) or !$session['loggedin']) {
            $returnString = '<button id="btnpost"><img src="postGrey.png" width=65 href="/post.php">';
        } else {
            $returnString = '<a href="post.php"><button id="btnpost"><img src="post.png" width=65 href="/post.php"></button></a>';
        }
        return $returnString;
    }

    function showCroak($userName, $croak, $datetime, $isUserCreator, $croakId, $showReportOption) {
        $croak =    filter_var($croak, FILTER_SANITIZE_SPECIAL_CHARS);
        $returnString = "";
        $userName .= "       "; //gap between edit button and username

        if($isUserCreator == true) {
            $returnString.= '<div class="croak">';
                $returnString.= '<h3>';
                    $returnString.= $userName;
                    $returnString.= '<a href="croak.php?croakId='.$croakId.'"><img src="options.png" width=20></a>';
                $returnString.= '</h3>';
                $returnString.= $croak;
                $returnString.= '<div class="datetime"><i>';
                $returnString.= $datetime;
                $returnString.= '</i></div>';
            $returnString.= '</div>';

            $returnString.= "<br>";

        } else if ($showReportOption == true){

            $returnString.= '<div class="croak">';
                $returnString.= '<h3>';
                    $returnString.= $userName;
                    $returnString.= '<a href="croak.php?croakId='.$croakId.'"><img src="report.png" width=20></a>';
                $returnString.= '</h3>';
                $returnString.= $croak;
                $returnString.= '<div class="datetime"><i>';
                $returnString.= $datetime;
                $returnString.= '</i></div>';
            $returnString.= '</div>';

            $returnString.= "<br>";

        } else {

            $returnString.= '<div class="croak">';
            $returnString.= '<h3>';
                $returnString.= $userName;
            $returnString.= '</h3>';
            $returnString.= $croak;
            $returnString.= '<div class="datetime"><i>';
            $returnString.= $datetime;
            $returnString.= '</i></div>';
        $returnString.= '</div>';
        $returnString.= "<br>";
        
        }

        return $returnString;
    }


?>