<?php
require('header.php');


?>
<div class="main">


    <div class="slider-container">
        <div class="menu">
            <label for="slide-dot-1"></label>
            <label for="slide-dot-2"></label>
            <label for="slide-dot-3"></label>
        </div>
        <input id="slide-dot-1" type="radio" name="slides" checked>
        <div class="slide slide-1"></div>
        <input id="slide-dot-2" type="radio" name="slides">
        <div class="slide slide-2"></div>
        <input id="slide-dot-3" type="radio" name="slides">
        <div class="slide slide-3"></div>
    </div>

   

    <p><?php echo file_get_contents('http://loripsum.net/api'); ?></p>
    <p><?php echo file_get_contents('http://loripsum.net/api'); ?></p>
    <p><?php echo file_get_contents('http://loripsum.net/api'); ?></p>
    <p><?php echo file_get_contents('http://loripsum.net/api'); ?></p>


</div>

<?php
require('footer.php');