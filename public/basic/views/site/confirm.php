<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<h1>Confirm</h1>
<?php if(Yii::$app->session->hasFlash('activation')):?>
    <span class="error_message">
        <?php echo Yii::$app->session->getFlash('activation'); ?>
    </span>
<?php endif; ?>
