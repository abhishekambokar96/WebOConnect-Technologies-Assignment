<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Calculator extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Calculator_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        //load first page calculator view with last calculations
        $data['calculations'] = $this->Calculator_model->get_last_calculations();
        $this->load->view('calculator_view', $data);
    }

    public function save()
    {
        $this->form_validation->set_rules('input', 'Input', 'required');
        $this->form_validation->set_rules('output', 'Output', 'required');
        if ($this->form_validation->run() === false) {
            $response['res_code'] = 1;
            $response['message'] = validation_errors();
            $response['method'] = "RegErrMsgNoReload";
            print_r(json_encode($response));
            exit;
        } else {
            $data = array(
                'input' => $this->input->post('input'),
                'output' => $this->input->post('output')
            );
            $res =  $this->Calculator_model->save_calculation($data);
            if ($res) {
                $response['res_code'] = 1;
                $response['message'] = "Data Saved Successfully";
                $response['method'] = "RegSuccMsg";
                print_r(json_encode($response));
                exit;
            }
        }
    }

    public function delete()
    {
        $id = $this->input->post('id');
        $this->Calculator_model->delete_calculation($id);
        $response['res_code'] = 1;
        $response['message'] = "Data Deleted Successfully";
        $response['method'] = "RegSuccMsg";
        print_r(json_encode($response));
        exit;
    }

    public function calculate()
    {
        $input = $this->input->post('input');

        $elements = preg_split('/([\+\-\*\/\(\)])/u', $input, -1, PREG_SPLIT_DELIM_CAPTURE);

        $numberStack = array();
        $operatorStack = array();

        $precedence = array(
            '+' => 1,
            '-' => 1,
            '*' => 2,
            '/' => 2,
            '(' => 0,
            ')' => 0
        );

        foreach ($elements as $element) {
            if (is_numeric($element)) {
                array_push($numberStack, $element);
            } elseif (array_key_exists($element, $precedence)) {
                while (count($operatorStack) > 0 && $precedence[end($operatorStack)] >= $precedence[$element]) {
                    $operator = array_pop($operatorStack);
                    $operand2 = array_pop($numberStack);
                    $operand1 = array_pop($numberStack);
                    $result = $this->applyOperator($operator, $operand1, $operand2);
                    array_push($numberStack, $result);
                }
                array_push($operatorStack, $element);
            }
        }

        while (count($operatorStack) > 0) {
            $operator = array_pop($operatorStack);
            $operand2 = array_pop($numberStack);
            $operand1 = array_pop($numberStack);
            $result = $this->applyOperator($operator, $operand1, $operand2);
            array_push($numberStack, $result);
        }

        $output = end($numberStack);
        echo json_encode(array('output' => $output));
    }

    private function applyOperator($operator, $operand1, $operand2)
    {
        switch ($operator) {
            case '+':
                return $operand1 + $operand2;
            case '-':
                return $operand1 - $operand2;
            case '*':
                return $operand1 * $operand2;
            case '/':
                return $operand1 / $operand2;
        }
    }
}
