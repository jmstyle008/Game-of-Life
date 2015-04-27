<?php

abstract class makeGame
{
    const arrayMax = 25;
    public $xAxis = self::arrayMax;
    public $yAxis = self::arrayMax;
    public $lifeArray = array();
    public $newLifeArray = array();

    abstract public function getLifeArray();

    abstract public function setLifeArray($array);

    public function __construct()
    {
        $this->makeLife();
    }

    public function makeLife()
    {
        for ($i = 0; $i < $this->xAxis; $i++) {
            for ($j = 0; $j < $this->yAxis; $j++) {
                $this->lifeArray[$i][$j] = rand(0, 1);
            }
        }
    }

    protected function rules()
    {
        $this->newLifeArray = $this->lifeArray;
        for ($i = 0; $i < $this->xAxis; $i++) {
            for ($j = 0; $j < $this->yAxis; $j++) {
                $this->_validateRules($i, $j);
            }
        }
    }

    private function _validateRules($i, $j)
    {
        $aliveNeighbors = 0;
        for ($limitXInf = $i - 1; $limitXInf <= $i + 1; $limitXInf++) {
            for ($limitYInf = $j - 1; $limitYInf <= $j + 1; $limitYInf++) {
                $value = (int)$this->lifeArray[$limitXInf][$limitYInf];
                if ((int)$value == 1)
                    $aliveNeighbors += 1;
            }
        }
        $currentValue = (int)$this->lifeArray[$i][$j];
        if ($currentValue == 1) {
            $aliveNeighbors = $aliveNeighbors - 1; //SO I WON'T COUNT MYSELF AS A NEIGHBOR
        }
        if ($aliveNeighbors < 2 && $currentValue == 1) { //UNDERPOPULATION = DEATH
            $this->newLifeArray[$i][$j] = 0;
        }
        if (($aliveNeighbors == 2 || $aliveNeighbors == 3) && $currentValue == 1) {//IF WE HAVE 2 OR 3 ALIVE I LIVE!
            $this->newLifeArray[$i][$j] = 1;
        }
        if ($aliveNeighbors > 3 && $currentValue == 1) { // IF GREATER THAN 3 ALIVE, OVER POPULATION, I'M DEAD
            $this->newLifeArray[$i][$j] = 0;
        }
        if ($aliveNeighbors == 3 && $currentValue == 0) { //IF 3 ALIVE AND I'M DEAD, I LIVE AGAIN
            $this->newLifeArray[$i][$j] = 1;
        }
    }

    protected function displayHtml()
    {
        $result = '<table >';
        for ($i = 0; $i < $this->xAxis; $i++) {
            $result .= '<tr>';
            for ($j = 0; $j < $this->yAxis; $j++) {
                $color = 'white';
                if ((bool)$this->newLifeArray[$i][$j])
                    $color = 'black';
                $result .= '<td style="background-color:' . $color . '; color:' . $color . ';">TTTT<input type="hidden" name=array[' . $i . '][' . $j . '] value="' . $this->newLifeArray[$i][$j] . '"></td>';
            }
            $result .= '</tr>';
        }
        return $result . '<table>';
    }
}

class gameOfLife extends makeGame
{
    private function _startGame()
    {
        $this->rules();
    }

    public function displayLifeArray()
    {

        return $this->displayHtml();
    }

    public function getLifeArray()
    {
        return $this->lifeArray;
    }

    public function setLifeArray($array)
    {
        $this->lifeArray = $array;
        if (empty($array)) {
            $this->makeLife();
        }
        $this->_startGame();
    }
}

$startGame = new gameOfLife();
$startGame->setLifeArray($startGame->getLifeArray());
if (array_key_exists('array', $_POST) && !empty($_POST['array'])) {
    $startGame->setLifeArray($_POST['array']);
}
echo json_encode(array('table' => $startGame->displayLifeArray(false)));