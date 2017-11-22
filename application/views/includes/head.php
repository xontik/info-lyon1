<!DOCTYPE html>
<html lang="fr">
<head>
    <title><?= $title ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <?php
    if (!empty($css)) {
        foreach ($css as $file) { ?>
            <link rel="stylesheet" type="text/css" href="<?= css_url($file) ?>">
            <?php
        }
    } else { ?>
        <link rel="stylesheet" type="text/css" href="<?= css_url('style') ?>">
        <?php
    } ?>
</head>
<body>
