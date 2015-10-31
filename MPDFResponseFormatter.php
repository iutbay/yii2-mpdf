<?php

namespace iutbay\yii2mpdf;

use yii\base\Component;
use yii\web\ResponseFormatterInterface;

/**
 * mPDF response formatter.
 * @author Kevin LEVRON <kevin.levron@gmail.com>
 */
class MPDFResponseFormatter extends Component implements ResponseFormatterInterface
{

    public $mode = '';
    public $format = 'A4';
    public $defaultFontSize = 0;
    public $defaultFont = '';
    public $marginLeft = 15;
    public $marginRight = 15;
    public $marginTop = 16;
    public $marginBottom = 16;
    public $marginHeader = 9;
    public $marginFooter = 9;
    public $orientation = 'P';
    public $options = [];
    public $dest = 'S';
    
    /**
     * @var string the Content-Type header for the response
     */
    public $contentType = 'application/pdf';

    /**
     * @inheritdoc
     */
    public function format($response)
    {
        $response->getHeaders()->set('Content-Type', $this->contentType);

        $mpdf = new \mPDF($this->mode,
            $this->format,
            $this->defaultFontSize,
            $this->defaultFont,
            $this->marginLeft,
            $this->marginRight,
            $this->marginTop,
            $this->marginBottom,
            $this->marginHeader,
            $this->marginFooter,
            $this->orientation
        );
        
        foreach ($this->options as $key => $option) {
            $mpdf->$key = $option;
        }

        $mpdf->WriteHTML($response->data);
        return $mpdf->Output('', $this->dest);
    }

}
