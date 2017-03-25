<?php define('SAVE_PRIVATE_DATA', true); ?>

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
<?php

function d($parametros, $fin = true)
{
    echo "<pre>";
    print_r($parametros);
    echo "</pre>";

    if ($fin) {
        die ('fin');
    }
}

include(__DIR__ . '/constantes.php');
include(__DIR__ . '/VBulletin.php');
include(__DIR__ . '/Searcher.php');
include(__DIR__ . '/Thread.php');
include(__DIR__ . '/Scraper.php');
include(__DIR__ . '/Page.php');

$searcher = new Searcher();
$threadsFromFile = $searcher->retrieveThreadsFromFile();

if ($_POST) {

    $error = false;

    $searcher->configSearch();
    ?>
    <div class="resultado"> <?php

        foreach ($searcher->getThreads() AS $thread) {

            ?>

            <div class="resultado_datos">

                <br/>HILO: <strong><?php echo $thread->getUrl() ?> </strong>
                <br/>USUARIO: <strong><?php echo $thread->getUserSearched() ?></strong>
                <br/>PÁGINA INICIO: <strong><?php echo $thread->getFirstPage() ?></strong>
                <br/>PÁGINA FINAL: <strong><?php echo $thread->getLastPage() ?></strong>
            </div>

            <?php

            $scraper = new Scraper($thread);
            $scraper->scrapThread();

            foreach ($scraper->getPages() as $page) {

                if ($page->isQuoted()) {
                    ?>
                    <span class="resultados citado">
                        <img src="roto2.png"/>&nbsp;&nbsp;
                        <?php echo $thread->getUserSearched() ?> ha sido CITADO en la página <strong><?php echo $page->getNumPage() ?></strong>
                        <a href="<?php echo $page->getUrl() ?>" target="_blank" style="color:green;font-weight:bold;" title="Ver el post">VER POST</a>
                    </span>

                    <?php
                } elseif ( ! $page->isQuoted()) {
                    ?>
                    <span class="resultados no_citado">
						<?php echo $thread->getUserSearched() ?> NO ha sido citado en la página <strong><?php echo $page->getNumPage() ?></strong>
                    </span>

                    <?php
                }
            }

        }

        ?> </div> <?php


}

@include(__DIR__ . '/view.php');

?>


</body>