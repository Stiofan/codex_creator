<?php

ob_start();?>
/**
* Return the formatted date.
*
* Return a formatted date from a date/time string according to WordPress date format. $date must be in format : 'Y-m-d H:i:s'.
*
* @since 1.0.0
* @param string $date must be in format: 'Y-m-d H:i:s'
* @return bool|int|string the formatted date
*/


<?php
$docblock = ob_get_clean();

$phpdoc = new \phpDocumentor\Reflection\DocBlock($docblock);


//echo $phpdoc->getShortDescription().'###'.$phpdoc->getContext();
if($phpdoc->isTemplateStart()){echo '###1';}else{echo '###0';}

//$phpdoc->appendTag('ignore');
print_r( $phpdoc->getTags());

foreach($phpdoc->getTags() as $tag){
	echo '###'.$tag->getContext().'###';
}
//	print_r( $phpdoc->getTagsByName('since'));

echo $phpdoc->getLongDescription();//getContext().'###';


