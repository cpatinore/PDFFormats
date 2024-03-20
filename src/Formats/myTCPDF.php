<?php

namespace PDFFormat\Formats;

use PDFFormat\Interfaces\FilePDF;
use TCPDF;

class MyTCPDF extends TCPDF implements FilePDF
{

    private $footerContent;

    public function createTable($headers, $data, $title, $i = 1, $style = "")
    {
        setlocale(LC_MONETARY, "en_US");

        $head = "";

        foreach ($headers as &$config) {
            $head .= '<th style="' . $config["style"] . '"><b>' . $config["title"] . "</b></th>";
        }

        $head = "<tr>$head</tr>";

        $body = "";
        $oddRow = false;
        foreach ($data as &$row) {
            $backgroundColor = $oddRow ? '#e6e6e6' : '#fff';
            $oddRow = !$oddRow;

            $body .= '<tr style="background-color: ' . $backgroundColor . ';">';


            foreach ($headers as $header => $config) {
                $body .= '<td style="' . $config["style"] . '">';

                if (isset ($row[$header])) {
                    switch ($config['type']) {
                        case 'money':
                            $body .= "$ " . number_format($row[$header], 0, ',', '.');
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

    public function Header()
    {
        if ($this->header_xobjid === false) {

            $this->header_xobjid = $this->startTemplate($this->w, $this->tMargin);
            $headerfont = $this->getHeaderFont();
            $headerdata = $this->getHeaderData();
            $this->y = $this->header_margin;

            if ($this->rtl) {
                $this->x = $this->w - $this->original_rMargin;
            } else {
                $this->x = $this->original_lMargin;
            }

            if (($headerdata['logo']) and ($headerdata['logo'] != K_BLANK_IMAGE)) {

                $this->Image($headerdata['logo'], '', '', $headerdata['logo_width']);

                $imgy = $this->getImageRBY();
            } else {
                $imgy = $this->y;
            }

            $cell_height = $this->getCellHeight($headerfont[2] / $this->k);

            if ($this->getRTL()) {
                $header_x = $this->original_rMargin + ($headerdata['logo_width'] * 1.1);
            } else {
                $header_x = $this->original_lMargin + ($headerdata['logo_width'] * 1.1);
            }

            $cw = $this->w - $this->original_lMargin - $this->original_rMargin - ($headerdata['logo_width'] * 1.1);
            $this->setTextColorArray($this->header_text_color);

            // header title
            $this->setFont($headerfont[0], 'B', $headerfont[2] + 1);
            $this->setX($header_x);
            $this->Cell($cw, $cell_height, $headerdata['title'], 0, 1, '', 0, '', 0);

            // header string
            $this->setFont($headerfont[0], $headerfont[1], $headerfont[2]);
            $this->setX($header_x);
            $this->MultiCell($cw, $cell_height, $headerdata['string'], 0, '', 0, 1, '', '', true, 0, false, true, 0, 'T', false);

            // print an ending header line
            $this->setLineStyle(array('width' => 0.85 / $this->k, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $headerdata['line_color']));
            $this->setY((2.835 / $this->k) + max($imgy, $this->y));

            if ($this->rtl) {
                $this->setX($this->original_rMargin);
            } else {
                $this->setX($this->original_lMargin);
            }
            $this->Cell(($this->w - $this->original_lMargin - $this->original_rMargin), 0, '', 'T', 0, 'C');
            $this->endTemplate();
        }
        // print header template
        $x = 0;
        $dx = 0;
        if (!$this->header_xobj_autoreset and $this->booklet and (($this->page % 2) == 0)) {
            // adjust margins for booklet mode
            $dx = ($this->original_lMargin - $this->original_rMargin);
        }
        if ($this->rtl) {
            $x = $this->w + $dx;
        } else {
            $x = 0 + $dx;
        }
        $this->printTemplate($this->header_xobjid, $x, 0, 0, 0, '', '', false);
        if ($this->header_xobj_autoreset) {
            // reset header xobject template at each page
            $this->header_xobjid = false;
        }
    }
}