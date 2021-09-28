<?php
require_once "vendor/autoload.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$accountname = $_ENV['ACCOUNT_NAME'];
$acckey = $_ENV['ACCOUNT_KEY'];

$connectionString = "DefaultEndpointsProtocol=https;AccountName=$accountname;AccountKey=$acckey;EndpointSuffix=core.windows.net";

// Create a blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);

try {
  $listBlobsOptions = new ListBlobsOptions();
} catch (ServiceException $e) {
  http_response_code($e->getCode());
  die();
}

$listBlobsOptions = new listBlobsOptions();
$emailStripped = "amberamouricloudcom";
$containerName = "userdata-" . $emailStripped;

do {
    $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
    foreach ($result->getBlobs() as $blob) {
      $requestedBlob = $blob->getName();
    }
    $listBlobsOptions->setContinuationToken($result->getContinuationToken());
} while($result->getContinuationToken());

$result = $blobClient->getBlobProperties($containerName, $requestedBlob);
$props = $result->getProperties();

header("Content-Type: " . $props->getContentType());
$blob = $blobClient->getBlob($containerName, $requestedBlob);
fpassthru($blob->getContentStream());
