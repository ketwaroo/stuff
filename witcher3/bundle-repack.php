<?php

ini_set('memory_limit', '4096M');
set_time_limit(0);

// usage php budle-repack.php <bundle file> <directory to import.>
// These are the defaults I use.
$out = isset($argv[1]) ? $argv[1] : __DIR__ . '/content/content0/bundles/xml.bundle';
$dir = isset($argv[2]) ? $argv[2] : __DIR__ . '/x';

if(!is_file($out) || !is_dir($dir))
{
    die('bundle file or import dir do not exist.');
}

// AlignmentUnused pads end of file to nearest 16
// header padded to nearest 4096 bytes
// files padded to nearest 4096 bytes.
// get all files recursively
$infiles = iterator_to_array(
    new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(
    $dir
    , RecursiveDirectoryIterator::SKIP_DOTS
    )
    )
);



$files = [];

$shortpad = 'AlignmentUnused';

$bodysize      = 0;
$finalbodysize = 0;

$countfiles      = count($infiles);
$infoblocksize   = $countfiles * 320;
$finalheadersize = padNumTo((32 + $infoblocksize), 4096);

$i = 1;
foreach($infiles as $k => $f)
{
    /* @var $f SplFileInfo */

    // key for infoblock
    // remove leading slash
    $ff = ltrim(str_replace(array($dir, '/'), ['', '\\'], $k), '\\/');
    var_dump($ff);

    $files[$ff] = [
        'content'   => file_get_contents($f->getPathname()),
        'size'      => $f->getSize(),
        'shortsize' => padNumTo($f->getSize(), 16),
        'longsize'  => padNumTo($f->getSize(), 4096),
        'timestamp' => $f->getMTime(),
    ];

    $files[$ff]['offset'] = $finalheadersize + $finalbodysize;

    $bodysize += $files[$ff]['size'];

    // recalc body size
    // do not pad last file. 
    $finalbodysize += ($i == $countfiles) ? $files[$ff]['shortsize'] : $files[$ff]['longsize'];

    $i++;
}

$finalfilesize = $finalheadersize + $finalbodysize;

// build headerblock. should be 32 char in length.
$headerblock = 'POTATO70';
$headerblock .= pack('V', $finalfilesize);
$headerblock .= pack('V', $bodysize);
$headerblock .= pack('V', $infoblocksize);
$headerblock .= "\x03\x00\x01\x00\x00\x13\x13\x13\x13\x13\x13\x13";

$infoblock = '';
$datablock = '';

$i = 1;
foreach($files as $f => $v)
{

    $c = file_get_contents($dir . '/' . $f);

    $infoblock.=str_pad($f, 256, "\x00", STR_PAD_RIGHT)
        . hex2bin(md5($v['content']))//hash? don't know what algo to use. doesn't seem to matter.
        . str_repeat("\x00", 4) //spacer
        . pack('V', strlen($c)) // file size
        . pack('V', strlen($c)) // zip size
        . pack('V', $v['offset']) // offset from start
        . pack('VV', $v['timestamp'], 0) // 64bit timestamp split into 2
        . str_repeat("\x00", 16) //spacer
        . str_repeat("\xFF", 4) // dummy?
        . pack('V', 0) // not zip.
    ;

    $padded = padStrTo($v['content'], 16, $shortpad);
    // do not pad last file.
    if($i < $countfiles)
    {
        $padded = padStrTo(
            $padded
            , 4096
            , "\x00"
        );
    }

    $datablock.= $padded;

    $i++;
}

$bin = padStrTo($headerblock . $infoblock, 4096, "\x00") . $datablock;

file_put_contents($out, $bin);

/**
 * increase $i to neareast multiple of $mult
 * 
 * @param int $i
 * @param int $mult
 * @return int
 */
function padNumTo($i, $mult)
{
    return intval(ceil($i / $mult) * $mult);
}
/**
 * pad string to neareast multiple of $mult using $with.
 * 
 * @param string $str
 * @param int $mult
 * @param string $with
 * @return string
 */
function padStrTo($str, $mult, $with)
{
    $len = padNumTo(strlen($str), $mult);
    return str_pad($str, $len, $with, STR_PAD_RIGHT);
}
//debug
function prntd($x)
{
    print_r($x);
    die;
}
