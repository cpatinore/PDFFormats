<?php

namespace PDFFormat\Interfaces;

interface FilePDF
{

        function SetAuthor($author);

        function SetTitle($title);

        function SetHeaderData($urlLogo, $wLogo, $hTitle, $hString);

        function SetMargins($left, $top, $right);

        function SetHeaderMargin($hMargin);

        function SetFooterMargin($fHeader);

        function setImageScale($ratioScale);

        function SetFont($font, $style, $size);

        function setFooterContent($content);

        function getAliasNumPage();

        function getAliasNbPages();

        function Header();

        function Footer();

        function AddPage();

        function writeHTML($html, $ln, $fill, $reseth, $cell, $align);

        function createTable($headers, $data, $title, $i, $style);

        function lastPage();

        function setHeaderFont($font);

        function setFooterFont($font);

        function Output($name, $dest);
}