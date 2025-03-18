<?php
include_once 'includes/header.php';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group c Members</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        tfoot td {
            font-weight: bold;
            background-color: #f2f2f2;
        }
    </style>
</head>

<div class="form-container">

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Registration Number</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tulinawe Ismail</td>
                <td>23/U/18058/EVE</td>
            </tr>
            <tr>
                <td>Boonabaana Bronia</td>
                <td>23/U/07647/EVE</td>
                <!-- <td><img src="assets/grouppics/Arnold.jpg" alt=""></td> -->
            </tr>
            <tr>
                <td>Mwesigwa Arnold</td>
                <td>23/U/24738/PS</td>
                <!-- <td><img src="assets/grouppics/Arnold.jpg" alt=""></td> -->
            </tr>
            <tr>
                <td>Naisanga Patricia</td>
                <td>23/U/13204/EVE</td>
                <!-- <td><img src="assets/grouppics/Arnold.jpg" alt=""></td> -->
            </tr>
            <tr>
                <td>Nakyambadde Mariam</td>
                <td>23/U/13953/EVE</td>
                <!-- <td><img src="assets/grouppics/Arnold.jpg" alt=""></td> -->
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">Total Students: 5</td>
            </tr>
        </tfoot>
    </table>
    <p>This group website has been uploaded to</p>
    <a href="http://groupc.rf.gd/UserManagementSystem">groupc.rf.gd/UserManagementSystem</a>
</div>

    <?php include_once 'includes/footer.php'; ?>
