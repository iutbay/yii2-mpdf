<?php

namespace iutbay\yii2mpdf;

use Yii;
use yii\base\Component;
use yii\web\ResponseFormatterInterface;
use yii\helpers\ArrayHelper;

/**
 * mPDF response formatter.
 * 
 * @author Kevin LEVRON <kevin.levron@gmail.com>
 */
class MPDFResponseFormatter extends Component implements ResponseFormatterInterface
{

    // mPDF construtor parameters
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

    // mPDF attributes
    public $options = [];

    // mPDF output parameters
    public $outputName = '';
    public $outputDest = 'I';

    /**
     * @var string the Content-Type header for the response
     */
    public $contentType = 'application/pdf';

    /**
     * @inheritdoc
     */
    public function format($response)
    {
        $this->formatMPDF($response);
    }

    /**
     * 
     * @param \yii\web\Response $response
     */
    public function formatMPDF($response)
    {
        $mpdf = new \mPDF(
            $this->mode,
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

        foreach ($this->options as $attribute => $value) {
            $mpdf->$attribute = $value;
        }

        if (is_string($response->data)) {
            $mpdf->WriteHTML($response->data);
        } else if (is_array($response->data)) {
            $this->outputName = ArrayHelper::getValue($response->data, 'outputName', $this->outputName);
            $this->outputDest = ArrayHelper::getValue($response->data, 'outputDest', $this->outputDest);
            if (isset($response->data['content'])) {
                $mpdf->WriteHTML($response->data['content']);
            }
        }

        $mpdf->Output($this->outputName, $this->outputDest);
    }

    /**
     * Set DI container class configuration
     */
    public static function setClassConfiguration($config)
    {
        Yii::$container->set(self::className(), $config);
    }

}
