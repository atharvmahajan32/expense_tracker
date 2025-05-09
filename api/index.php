<?php
session_start();

if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 0.0;
}
if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [];
}

$totalIncome = 0.0;
$totalExpense = 0.0;
foreach ($_SESSION['transactions'] as $transaction) {
    if ($transaction['type'] === 'income') {
        $totalIncome += $transaction['amount'];
    } elseif ($transaction['type'] === 'expense') {
        $totalExpense += $transaction['amount'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['description'], $_POST['amount'], $_POST['type'])) {
    $description = trim($_POST['description']);
    $amount = floatval($_POST['amount']);
    $type = $_POST['type'];

   
    $transaction = [
        'description' => $description,
        'type' => $type,
        'amount' => $amount,
        'date' => date("Y-m-d H:i:s")
    ];

    if ($type === 'income') {
        $_SESSION['balance'] += $amount;
    } elseif ($type === 'expense') {
        $_SESSION['balance'] -= $amount;
    }

    $_SESSION['transactions'][] = $transaction;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $index = intval($_POST['delete']);
    if (isset($_SESSION['transactions'][$index])) {
        $transaction = $_SESSION['transactions'][$index];
        if ($transaction['type'] === 'income') {
            $_SESSION['balance'] -= $transaction['amount'];
        } elseif ($transaction['type'] === 'expense') {
            $_SESSION['balance'] += $transaction['amount'];
        }
        array_splice($_SESSION['transactions'], $index, 1);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$totalIncome = 0.0;
$totalExpense = 0.0;
foreach ($_SESSION['transactions'] as $transaction) {
    if ($transaction['type'] === 'income') {
        $totalIncome += $transaction['amount'];
    } elseif ($transaction['type'] === 'expense') {
        $totalExpense += $transaction['amount'];
    }
}

if (isset($_POST['terminate'])) {
    session_unset();
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Tracker</title>
    <style>
                * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #F4F4F9;
            color: #333;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
        }

        .header {
            width: 100%;
            padding: 15px;
            background-color: #024950;
            color: #FFFFFF;
            text-align: center;
            font-size: 24px;
        }

        .main-container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #FFFFFF;
            border: 1px solid #AFDDE5;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .main-container h2 {
            text-align: center;
            color: #003135;
            margin-bottom: 20px;
        }

        .balance-container {
            display: flex;
            justify-content: space-around;
            background-color: #AFDDE5;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            color: #003135;
            font-size: 18px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-section, .transactions-section {
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        form input, form button, form select {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #AFDDE5;
            border-radius: 5px;
        }

        form button {
            background-color: #024950;
            color: #FFFFFF;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #964734;
        }

        .transactions-section table {
            width: 100%;
            border-collapse: collapse;
        }

        .transactions-section table, .transactions-section th, .transactions-section td {
            border: 1px solid #AFDDE5;
        }

        .transactions-section th, .transactions-section td {
            padding: 10px;
            text-align: left;
        }

        .transactions-section th {
            background-color: #AFDDE5;
        }

        .transactions-section td.description {
            max-width: 200px;
            overflow-x: auto;
            white-space: nowrap;
        }

        .delete-button {
            background-color: #FF0000;
            color: #FFFFFF;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .delete-button:hover {
            background-color: #CC0000;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background-color: #024950;
            color: #FFF;
        }
    </style>
</head>
<body>
    <div class="header">EXPENSE TRACKER</div>

    <div class="main-container">
        <h2>Track Your Money</h2>

        <div class="balance-container">
            <div>
                <div>Total Income</div>
                ₹<?php echo number_format($totalIncome, 2); ?>
            </div>
            <div>
                <div>Balance</div>
                ₹<?php echo number_format($_SESSION['balance'], 2); ?>
            </div>
            <div>
                <div>Total Expense</div>
                ₹<?php echo number_format($totalExpense, 2); ?>
            </div>
        </div>

        <div class="form-section">
            <h3>Add Transaction</h3>
            
            <form action="" method="POST" onsubmit="return validateForm(event)">
                <input type="text" id="description" name="description" placeholder="Description" required>
                <input type="number" step="0.01" id="amount" name="amount" placeholder="Amount" required>
                <select id="type" name="type" required>
                    <option value="">Select Type</option>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
                <button type="submit">Add Transaction</button>
            </form>
        </div>

        <div class="transactions-section">
            <h3>Previous Transactions</h3>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_reverse($_SESSION['transactions'], true) as $index => $transaction): ?>
                        <tr>
                            <td class="description"><?php echo htmlspecialchars($transaction['description']); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($transaction['type'])); ?></td>
                            <td>₹<?php echo number_format($transaction['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($transaction['date']); ?></td>
                            <td>
                                <form action="" method="POST" style="display:inline;">
                                    <button type="submit" name="delete" value="<?php echo $index; ?>" class="delete-button">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <form action="" method="POST">
                <button type="submit" name="terminate" style="padding: 10px 20px; background-color: #964734; color: #FFF; border: none; border-radius: 5px; cursor: pointer;">
                    Terminate Session
                </button>
            </form>
        </div>
    </div>

    <footer class="footer">Atharv Mahajan (0827CS221056)</footer>
    <script src="../public/script.js"></script>
</body>
</html>