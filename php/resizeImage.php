<?php

function resizePreservingAspectRatio($img, $targetWidth, $targetHeight)
{
    $srcWidth = imagesx($img);
    $srcHeight = imagesy($img);

    // Determine new width / height preserving aspect ratio
    $srcRatio = $srcWidth / $srcHeight;
    $targetRatio = $targetWidth / $targetHeight;
    if (($srcWidth <= $targetWidth) && ($srcHeight <= $targetHeight))
    {
        $imgTargetWidth = $srcWidth;
        $imgTargetHeight = $srcHeight;
    }
    else if ($targetRatio > $srcRatio)
    {
        $imgTargetWidth = (int) ($targetHeight * $srcRatio);
        $imgTargetHeight = $targetHeight;
    }
    else
    {
        $imgTargetWidth = $targetWidth;
        $imgTargetHeight = (int) ($targetWidth / $srcRatio);
    }

    // Creating new image with desired size
    $targetImg = imagecreatetruecolor($targetWidth, $targetHeight);

    // Add transparency if your reduced image does not fit with the new size
    $targetTransparent = imagecolorallocate($targetImg, 255, 255, 255);
    imagefill($targetImg, 0, 0, $targetTransparent);
    imagecolortransparent($targetImg, $targetTransparent);

    // Copies image, centered to the new one (if it does not fit to it)
    imagecopyresampled(
       $targetImg, $img, ($targetWidth - $imgTargetWidth) / 2, // centered
       ($targetHeight - $imgTargetHeight) / 2, // centered
       0, 0, $imgTargetWidth, $imgTargetHeight, $srcWidth, $srcHeight
    );

    return $targetImg;
}

?>
