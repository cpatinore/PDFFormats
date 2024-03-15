<?php

namespace PDFFormat\Formats;

use PDFFormat\Interfaces\FilePDF;
use TCPDF;

class myTCPDF extends TCPDF implements FilePDF
{

    private $footerContent;

    public function createTable($headers, $data, $title, $i = 1, $style = "")
    {
        setlocale(LC_MONETARY, "en_US");

        $head = "";

        foreach ($headers as &$config) {
            $head .= '<th width="' . $config["width"] . '"><b>' . $config["title"] . "</b></th>";
        }

        $head = "<tr>$head</tr>";

        $body = "";
        $oddRow = false;
        foreach ($data as &$row) {
            $backgroundColor = $oddRow ? '#e6e6e6' : '#fff';
            $oddRow = !$oddRow;

            $body .= '<tr style="background-color: ' . $backgroundColor . ';">';


            foreach ($headers as $header => $config) {
                $body .= '<td width="' . $config["width"] . '">';

                if (isset($row[$header])) {
                    switch ($config['type']) {
                        case 'money':
                            $body .= "$" . number_format($row[$header], 0, ',', '.');
                            break;

                        case 'count':
                            $body .= $i;
                            break;

                        default:
                            $body .= $row[$header];
                            break;
                    }
                } else {
                    $body .= "";
                }

                $body .= '</td>';

            }

            $body .= "</tr>";
            $i++;
        }

        $cols = count($headers);

        $table = <<<EOF
                    $style
                    <div><table class="table" cellpadding="2" cellspacing="0" border="0.5" style="margin:0; padding:0;">
                        
                            <thead>
                            <tr>
                                <th colspan="$cols" >
                                    $title
                                </th>
                            </tr>
                            $head
                            </thead>
                            $body
                    </table>
                    </div>
                EOF;

        $this->writeHTML($table, true, false, true, false, '');
    }

    public function setFooterContent($content)
    {
        $this->footerContent = $content;
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, $this->footerContent, 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

}