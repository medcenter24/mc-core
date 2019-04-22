<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Services;


use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Form;
use Illuminate\Database\Eloquent\Model;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class FormService
{
    /**
     * Filesystem constants
     */
    const PDF_DISK = 'pdfForms';
    const PDF_FOLDER = 'pdfForms';

    /**
     * @param Form $form
     * @param Model $source
     * @return mixed
     * @throws InconsistentDataException
     */
    public function getPdfPath(Form $form, Model $source)
    {
        $uniq = $this->getCacheableUniqValue($source) . '.pdf';

        if (!\Storage::disk(self::PDF_DISK)->exists($uniq)) {
            $this->toPdf($form, $source, $uniq);
        }

        return \Storage::disk(self::PDF_DISK)->path($uniq);
    }

    public function toPdf(Form $form, Model $source, $path = 'file.pdf')
    {
        try {
            $mpdf = new Mpdf([
                'tempDir' => storage_path('tmp'),
                'debug' => true,
                'useSubstitutions' => false,
                'simpleTables' => true,
                'use_kwt' => true,
                'shrink_tables_to_fit' => 1,
                'showImageErrors' => true,
                /*'allowCJKorphans' => false,
                'allowCJKoverflow' => true,*/
                /*'ignore_table_percents' => true,
                'ignore_table_widths' => true,*/
                /*'keepColumns' => true,
                'keep_table_proportions' => true,*/
                // 'justifyB4br' => true,
                'margin_left' => 3,
                'margin_right' => 5,
                'margin_top' => 9,
                'margin_bottom' => 1,
                'margin_header' => 0,
                'margin_footer' => 0,
            ]);
            $mpdf->WriteHTML($this->getHtml($form, $source));

            $mpdf->SetTitle('Form ' . $form->title . ' (' . $source->id . ')');
            $mpdf->SetAuthor(config('name'));
            /*$mpdf->SetWatermarkText("Paid");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.1;*/

            /*if ($this->report->hasDocuments()) {
                $mpdf->AddPage();
                $mpdf->WriteHTML($this->htmlDocuments());
            }*/
            $mpdf->SetDisplayMode('fullpage');

            $mpdf->Output(\Storage::disk(self::PDF_DISK)->path($path), Destination::FILE);
        } catch (\Mpdf\MpdfException $e) {
            \Log::debug($e->getMessage());
        }
    }

    /**
     * The value which perform state of the statistic to be cached and do not generated all the times
     * @param Model $model
     * @return string
     * @throws InconsistentDataException
     */
    private function getCacheableUniqValue(Model $model)
    {
        $modelClass = get_class($model);
        switch ($modelClass) {
            case Accident::class :
                $value = $model->ref_num . '_' . $model->updated_at->format('Ymd_His');
                break;
            default:
                throw new InconsistentDataException('Undefined model ' . $modelClass);
        }
        return $value;
    }

    /**
     * @param Form $form
     * @param Model $source
     * @return string
     * @throws InconsistentDataException
     */
    public function getHtml(Form $form, Model $source): string
    {
        $variables = [];
        if ($form->variables) {
            $variables = json_decode($form->variables);
            $variables = $variables ? : [];
            $variables = array_unique($variables);
        }
        $this->checkModel($form, $source);
        $this->checkVariables($variables, $form);
        $values = $this->getValues($source, $variables);

        $template = $form->template;
        foreach ($values as $key => $value) {
            $template = str_replace($key, $value, $template);
        }

        return $template;
    }

    /**
     * Getting values of the variables for the current model
     * @param Model $model
     * @param array $variables
     * @return array
     * @throws InconsistentDataException
     */
    private function getValues(Model $model, array $variables)
    {
        $values = [];
        foreach ($variables as $var) {
            $values[$var] = $this->getValue($model, $var);
        }
        return $values;
    }

    /**
     * @param Model $model
     * @param string $var
     * @return string
     * @throws InconsistentDataException
     */
    private function getValue(Model $model, $var = '')
    {
        $modelClass = get_class($model);
        switch ($modelClass) {
            case Accident::class :
                $value = $this->getAccidentValue($model, $var);
                break;
            default:
                throw new InconsistentDataException('Undefined model ' . $modelClass);
        }
        return $value;
    }

    /**
     * @param Accident $accident
     * @param $var
     * @return string
     * @throws InconsistentDataException
     */
    private function getAccidentValue(Accident $accident, $var)
    {
        switch ($var) {
            case ':doctor.name':
                $val = $accident->caseable->doctor ? $accident->caseable->doctor->name : '_Doctor Name_';
                break;
            case ':hospital.title':
                $val = $accident->caseable->hospital ? $accident->caseable->hospital->title : '_Hospital Title_';
                break;
            case ':patient.name':
                $val = $accident->patient ? $accident->patient->name : '_Patient Name_';
                break;
            case ':ref.number':
                $val = $accident->ref_num;
                break;
            default: throw new InconsistentDataException('Variable is not defined: ' . $var);
        }
        return $val;
    }

    /**
     * @param Form $form
     * @param Model $model
     * @throws InconsistentDataException
     */
    private function checkModel(Form $form, Model $model)
    {
        if ($form->formable_type != get_class($model)) {
            throw new InconsistentDataException('This model not supported by this form');
        }
    }

    /**
     * @param array $vars
     * @param Form $form
     * @throws InconsistentDataException
     */
    private function checkVariables(array $vars, Form $form)
    {
        $allowed = $this->getAllowedVariables($form);
        if ( count($diff = array_diff($vars, $allowed)) ) {
            throw new InconsistentDataException('Unsupported Form variables ' . print_r($diff, 1));
        }
    }

    /**
     * @param Form $form
     * @throws InconsistentDataException
     * @return array
     */
    private function getAllowedVariables(Form $form)
    {
        switch ($form->formable_type) {
            case Accident::class :
                $variables = $this->getAllowedForAccidentVariables();
                break;
            default: throw new InconsistentDataException('Implementation of this type of source `' . $sourceClass . '` does not exist.');
        }

        return $variables;
    }

    private function getAllowedForAccidentVariables()
    {
        return [
            ':doctor.name',
            ':hospital.title',
            ':patient.name',
            ':company.name',
            ':ref.number'
        ];
    }
}
