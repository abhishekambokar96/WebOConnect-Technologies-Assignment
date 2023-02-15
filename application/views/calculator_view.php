<html lang="en">

<head>
    <style>
        label {
            font-weight: 600;
            font-size: 15px;
        }

        .button1 {
            width: 50px !important;
            height: 50px !important;
        }
    </style>

</head>

<?php include __DIR__ . '/main_header_includes.php'; ?>

<body>
    <div class="page-content pt-0" style="font-family:'Roboto';">

        <div class="content-wrapper">

            <div class="content">

                <h1 style="font-weight:700; text-align:center;">CALCULATOR<br></h1>
                <br>
                <div class="card" style="width: 90%;margin-left: 65px;border-top-right-radius: 15px;border-top-left-radius: 15px;">

                    <div class="card-header bg-dark header-elements-inline" style="border-top-right-radius: 15px;border-top-left-radius: 15px;">
                        <h5 class="card-title"><b>CALCULATOR</b> </h5>
                    </div>

                    <div class="card-body">
                        <div class="calculator_body">
                            <!-- Input field -->
                            <label>Enter Input : <span class="required">*</span></label>
                            <input type="text" id="input">
                            <br>

                            <!-- Calculator buttons -->
                            <button class="btn btn-light btn-block button1 operator" data-operator="(">(</button>
                            <button class="btn btn-light btn-block button1 operator" data-operator=")">)</button>
                            <button class="btn btn-light btn-block button1 operator " data-operator="%">%</button>
                            <button class="btn btn-light btn-block button1 operator" id="clear-button">CE</button>
                            <br>
                            <button class="btn btn-light btn-block button1 number" data-number="7">7</button>
                            <button class="btn btn-light btn-block button1 number" data-number="8">8</button>
                            <button class="btn btn-light btn-block button1 number" data-number="9">9</button>
                            <button class="btn btn-light btn-block button1 operator" data-operator="/">/</button>
                            <br>
                            <button class="btn btn-light btn-block button1 number" data-number="4">4</button>
                            <button class="btn btn-light btn-block button1 number" data-number="5">5</button>
                            <button class="btn btn-light btn-block button1 number" data-number="6">6</button>
                            <button class="btn btn-light btn-block button1 operator" data-operator="*">*</button>
                            <br>
                            <button class="btn btn-light btn-block button1 number" data-number="1">1</button>
                            <button class="btn btn-light btn-block button1 number" data-number="2">2</button>
                            <button class="btn btn-light btn-block button1 number" data-number="3">3</button>
                            <button class="btn btn-light btn-block button1 operator" data-operator="-">-</button>
                            <br>
                            <button class="btn btn-light btn-block button1 number" data-number="0">0</button>
                            <button class="btn btn-light btn-block button1 decimal" data-number=".">.</button>
                            <button class="btn bg-success button1" id="equal-button">=</button>
                            <button class="btn btn-light btn-block button1 operator" data-operator="+">+</button><br>

                        </div>
                        <br>
                        <!-- Last Five Calculations Table -->
                        <h3 style="text-align:center;">Last Five Calculations</h3>
                        <div class="row mt-10">
                            <div class="table-responsive">
                                <table class="table table-bordered table-columned last-calculations" style="text-align:center;">

                                    <thead>
                                        <tr>
                                            <th>Input</th>
                                            <th>Answer</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($calculations as $calculation) : ?>
                                            <tr>
                                                <td><?php echo $calculation->input; ?></td>
                                                <td><?php echo $calculation->output; ?></td>
                                                <td>
                                                    <button class="btn btn-danger btn-icon btn-sm button1 delete" data-id="<?php echo $calculation->id; ?>"><i class="icon-trash" style="color: #fff;"> </i></button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/main_footer_includes.php'; ?>

    <!-- Javascript -->

    <script type="text/javascript">
        $(document).ready(function() {
            //load 5 calculations 
            loadCalculations();
            //fetch number
            $('.number').click(function() {
                var number = $(this).data('number');
                var input = $('#input').val();
                $('#input').val(input + number);
            });
            //clear button
            $('#clear-button').click(function() {
                $('#input').val('');
            });
            //decimal button
            $('.decimal').click(function() {
                var input = $('#input').val();
                // Check if input already contains a decimal point
                if (input.indexOf('.') === -1) {
                    $('#input').val(input + '.');
                }
            });

            // equal button perform operation
            $('#equal-button').click(function() {
                var input = $('#input').val();
                var result = evaluate(input);
                $('#input').val(result);
                // Save calculation using ajx
                var url = '<?php echo base_url() . 'calculator/save' ?>';
                var data = '{"input":"' + input + '","output":"' + result + '"}';
                customAjax(url, data);
            });

            // Delete button
            $('.delete').click(function() {
                var id = $(this).data('id');
                swal({
                    title: "Attention",
                    text: "This Data Will Not recover!",
                    type: "warning",
                    allowOutsideClick: false,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Confirm.',
                    cancelButtonColor: '#969696',
                    cancelButtonText: 'Cancel'
                }).then(function() {
                    var url = '<?php echo base_url() . 'calculator/delete' ?>';
                    var data = '{"id":"' + id + '"}';
                    customAjax(url, data);
                });
            });

            // operators button
            $('.operator').click(function() {
                var operator = $(this).data('operator');
                var input = $('#input').val();
                if (input == '') {
                    if (operator != "(") {
                        return;
                    }

                }
                var lastChar = input[input.length - 1];
                if (lastChar == '+' || lastChar == '-' || lastChar == '*' || lastChar == '/' || lastChar ==
                    '(' || lastChar == ')') {
                    return;
                }
                $('#input').val(input + operator);
            });

            //calculate percent button
            $('#percent-button').click(function() {
                var input = $('#input').val();
                if (input == '') {
                    return;
                }
                // Check if last character of input is a number
                var lastChar = input[input.length - 1];
                if (isNaN(lastChar)) {
                    return;
                }
                var percent = parseFloat(input) / 100;
                $('#input').val(percent);
            });

        });

        // Evaluate input string and return result
        function evaluate(input) {
            try {
                // Use eval to perform calculation
                var result = eval(input);
                // Check if result is valid number
                if (isNaN(result)) {
                    return 'Invalid input';
                } else if (result == Infinity) {
                    return 'Infinity';
                } else if (result == -Infinity) {
                    return '-Infinity';
                } else {
                    return result;
                }
            } catch (e) {
                return 'Invalid input';
            }
        }

        // Load last five calculations using above call
        function loadCalculations() {
            $.ajax({
                url: "<?php echo base_url('calculator'); ?>",
                type: 'get',
                dataType: 'json',
                success: function(result) {
                    if (result.status == 'success') {
                        var tableRows = '';
                        result.calculations.forEach(function(calculation) {
                            tableRows += '<tr><td>' + calculation.input + '</td><td>' + calculation
                                .output +
                                '</td><td><button class="delete" data-id="' + calculation.id +
                                '">Delete</button></td></tr>';
                        });
                        $('table tbody').html(tableRows);
                    } else {
                        alert(result.message);
                    }
                }
            });
        }
    </script>
</body>

</html>