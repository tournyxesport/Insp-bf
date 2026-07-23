<?php
/**
 * save.php — Point de sauvegarde optionnel pour l'espace Admin du site INSP.
 *
 * Ce script reçoit le JSON envoyé par admin.html et l'écrit directement
 * dans siteData.json sur le serveur. Cela rend les modifications de
 * l'administrateur visibles immédiatement pour TOUS les visiteurs du site,
 * sans avoir à télécharger/re-uploader le fichier manuellement.
 *
 * PRÉREQUIS :
 *  - Un hébergement qui supporte PHP (très répandu, y compris en hébergement
 *    mutualisé standard).
 *  - Le fichier siteData.json doit être accessible en écriture par PHP
 *    (permissions 664 ou 666 selon la configuration de l'hébergeur).
 *
 * SÉCURITÉ :
 *  - Ce script est volontairement simple. Il ne fait AUCUNE vérification de
 *    mot de passe côté serveur : la protection se fait uniquement côté
 *    navigateur dans admin.html (voir README.md).
 *  - Pour un site en production avec plusieurs administrateurs, il est
 *    recommandé de mettre en place une vraie authentification côté serveur
 *    (session PHP, .htaccess, jeton d'API, etc.) avant d'exposer ce fichier.
 *  - Si vous ne souhaitez pas activer cette fonctionnalité, supprimez
 *    simplement ce fichier : admin.html basculera automatiquement sur le
 *    mode "téléchargement manuel du JSON".
 */

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if ($data === null) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON invalide']);
    exit;
}

// Validation minimale de la structure attendue
if (!isset($data['site']) || !isset($data['pages']) || !isset($data['menu']) || !isset($data['news'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Structure de données incomplète']);
    exit;
}

$target = __DIR__ . '/siteData.json';

// Sauvegarde d'une copie de sécurité avant écriture
if (file_exists($target)) {
    @copy($target, __DIR__ . '/siteData.backup.json');
}

$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if (@file_put_contents($target, $json) === false) {
    http_response_code(500);
    echo json_encode(['error' => "Impossible d'écrire siteData.json — vérifiez les permissions du fichier."]);
    exit;
}

echo json_encode(['success' => true]);
