<?php
define('SAVE_PRIVATE_DATA', false);
define('SEPARADOR', ':::');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="es">
<head>
    <title>Quote Fc</title>
    <meta name="Description" content="Buscador de citas a tus posts en Forocoches"/>
    <meta name="robots" content="noarchive,noindex,nofollow"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="stylesheet" href="estilos.css" type="text/css" media="screen" charset="utf-8"/>
</head>
<body>
<div class="resultado">

    <?php
    include(__DIR__ . '/VBulletin.php');
    include(__DIR__ . '/Searcher.php');
    include(__DIR__ . '/Thread.php');
    include(__DIR__ . '/Scraper.php');
    include(__DIR__ . '/Page.php');

    $searcher = new Searcher();
    $threadsFromFile = $searcher->retrieveThreadsFromFile();

    if ($_POST) {

        $searcher->configSearch();

        foreach ($searcher->getThreads() AS $thread) {

            include(__DIR__ . '/view_info_thread.php');

            $scraper = new Scraper($thread);
            $scraper->scrapThread();

            foreach ($scraper->getPages() as $page) {

                if ($page->isQuoted()) {
                    include(__DIR__ . '/view_quoted.php');
                } elseif ( ! $page->isQuoted()) {
                    include(__DIR__ . '/view_not_quoted.php');
                }
            }

        }

    }

    ?>
</div>
<?php

@include(__DIR__ . '/view.php');

?>


</body>