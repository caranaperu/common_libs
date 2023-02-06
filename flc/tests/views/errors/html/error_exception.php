<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">

    <h4>An uncaught Exception was encountered</h4>

    <p>code: <?php echo $code; ?></p>
    <p>Type: <?php echo $type; ?></p>
    <p>Message: <?php echo $message; ?></p>
    <p>Filename: <?php echo $file; ?></p>
    <p>Line Number: <?php echo $line; ?></p>

    <?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === true): ?>

        <p>Backtrace:</p>
        <?php foreach ($trace as $error): ?>

            <!--		--><?php //if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>
            <?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>

                <p style="margin-left:10px">
                    File: <?php echo $error['file']; ?><br/>
                    Line: <?php echo $error['line']; ?><br/>
                    Function: <?php echo $error['function']; ?>
                </p>
            <?php endif ?>

        <?php endforeach ?>

    <?php endif ?>

</div>