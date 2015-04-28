<?php

abstract class makeGame
{
    const arrayMax = 25;
    public $xAxis = self::arrayMax;
    public $yAxis = self::arrayMax;
    public $lifeArray = array();
    public $newLifeArray = array();

    abstract public function getLifeArray();

    public function setLifeArray($array)
    {
        $this->lifeArray = (empty($array)) ? $this->makeLife() : $array;
    }

    public function __construct()
    {
        $this->makeLife();
    }

    public function makeLife()
    {
        for ($i = 0; $i < $this->xAxis; $i++)
            for ($j = 0; $j < $this->yAxis; $j++)
                $this->lifeArray[$i][$j] = rand(0, 1);
    }

    protected function rules()
    {
        $this->newLifeArray = $this->lifeArray;
        for ($i = 0; $i < $this->xAxis; $i++)
            for ($j = 0; $j < $this->yAxis; $j++)
                $this->_validateRules($i, $j);
    }

    private function _checkNeighbors($i, $j)
    {
        $aliveNeighbors = 0;
        for ($limitXInf = $i - 1; $limitXInf <= $i + 1; $limitXInf++)
            for ($limitYInf = $j - 1; $limitYInf <= $j + 1; $limitYInf++)
                $aliveNeighbors += ((bool)$this->lifeArray[$limitXInf][$limitYInf]) ? 1 : 0;
        return $aliveNeighbors;
    }

    private function _validateRules($i, $j)
    {
        $aliveNeighbors = ((bool)$this->lifeArray[$i][$j]) ? $this->_checkNeighbors($i, $j) - 1 : $this->_checkNeighbors($i, $j);
        if ($aliveNeighbors > 3 || $aliveNeighbors < 2 && (bool)$this->lifeArray[$i][$j])  //LONELINESS || UNDERPOPULATION = DEATH
            $this->newLifeArray[$i][$j] = 0;
        if (($aliveNeighbors == 2 || $aliveNeighbors == 3) && (bool)$this->lifeArray[$i][$j]) //IF WE HAVE 2 OR 3 ALIVE I LIVE!
            $this->newLifeArray[$i][$j] = 1;
        if ($aliveNeighbors == 3 && !(bool)$this->lifeArray[$i][$j])  //IF 3 ALIVE AND I'M DEAD, I LIVE AGAIN
            $this->newLifeArray[$i][$j] = 1;
    }

    public function displayLifeArray()
    {
        $result = '<table>';
        for ($i = 0; $i < $this->xAxis; $i++) {
            $result .= '<tr>';
            for ($j = 0; $j < $this->yAxis; $j++) {
                $color = (bool)$this->newLifeArray[$i][$j] ? 'black' : 'white';
                $result .= '<td style="background-color:' . $color . '; color:' . $color . ';">**<input type="hidden" name=array[' . $i . '][' . $j . '] value="' . $this->newLifeArray[$i][$j] . '"></td>';
            }
            $result .= '</tr>';
        }
        return $result . '<table>';
    }
}

class gameOfLife extends makeGame
{
    public function getLifeArray()
    {
        return $this->lifeArray;
    }

    public function setLifeArray($array)
    {
        parent::setLifeArray($array);
        $this->rules();
    }
}

$startGame = new gameOfLife();
$startGame->setLifeArray($startGame->getLifeArray());
if (array_key_exists('array', $_POST) && !empty($_POST['array']))
    $startGame->setLifeArray($_POST['array']);
echo json_encode(array('table' => $startGame->displayLifeArray()));