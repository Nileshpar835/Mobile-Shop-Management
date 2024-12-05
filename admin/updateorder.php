<?php
session_start();

include_once 'include/config.php';
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    $oid = intval($_GET['oid']);
    if (isset($_POST['submit2'])) {
        $status = $_POST['status'];
        $remark = $_POST['remark']; //space char

        $query = mysqli_query($con, "insert into ordertrackhistory(orderId,status,remark) values('$oid','$status','$remark')");
        $sql = mysqli_query($con, "update orders set orderStatus='$status' where id='$oid'");
        echo "<script>alert('Order updated sucessfully...');</script>";
//}
    }
    ?>
    <script language="javascript" type="text/javascript">
        function f2()
        {
            window.close();
        }

        function f3()
        {
            window.print();
        }
    </script>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <title>Update Compliant</title>
            <link href="style.css" rel="stylesheet" type="text/css" />
            <link href="anuj.css" rel="stylesheet" type="text/css">
                <style>
                    /* General Reset and Box Model */
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                        font-family: Arial, sans-serif;
                    }

                    /* Body */
                    body {
                        background: #f4f4f9;
                        color: #333;
                        padding: 20px;
                    }

                    /* Main Container */
                    div {
                        margin-left: 50px;
                        background: #fff;
                        border-radius: 10px;
                        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                        padding: 20px;
                    }

                    /* Table Styling */
                    table {
                        width: 100%;
                        margin-top: 10px;
                        border-collapse: collapse;
                    }

                    table td {
                        padding: 10px;
                        text-align: left;
                    }

                    /* Heading Styles */
                    .fontpink2 {
                        font-size: 24px;
                        color: #d9534f;
                        font-weight: bold;
                        text-align: center;
                        margin-bottom: 20px;
                    }

                    /* Label Styling */
                    .fontkink1 {
                        font-size: 16px;
                        font-weight: bold;
                        color: #333;
                    }

                    /* Table Data */
                    .fontkink {
                        font-size: 16px;
                        color: #555;
                    }

                    /* Button Styling */
                    input[type="submit"] {
                        padding: 10px 20px;
                        background-color: #d9534f;
                        color: white;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                        transition: background-color 0.3s ease;
                    }

                    input[type="submit"]:hover {
                        background-color: #c9302c;
                    }

                    textarea {
                        width: 100%;
                        padding: 10px;
                        border: 1px solid #ccc;
                        border-radius: 5px;
                        resize: vertical;
                    }

                    select {
                        width: 100%;
                        padding: 10px;
                        border-radius: 5px;
                        border: 1px solid #ccc;
                        background-color: #f8f9fa;
                    }

                    /* Table Row Hover Effects */
                    table tr:nth-child(even) {
                        background-color: #f9f9f9;
                    }

                    table tr:hover {
                        background-color: #f1f1f1;
                        cursor: pointer;
                    }

                    /* Form Inputs */
                    input[type="text"], input[type="number"], input[type="date"], input[type="email"], input[type="password"], input[type="textarea"] {
                        padding: 10px;
                        margin: 10px 0;
                        border-radius: 5px;
                        width: 100%;
                        border: 1px solid #ddd;
                    }

                    textarea {
                        height: 100px;
                        resize: none;
                    }

                    /* Responsive Styles */
                    @media (max-width: 768px) {
                        div {
                            margin-left: 20px;
                            padding: 15px;
                        }

                        table {
                            width: 100%;
                        }

                        input[type="submit"], textarea, select {
                            width: 100%;
                        }
                    }


                </style>
        </head>
        <body>

            <div style="margin-left:50px;">
                <form name="updateticket" id="updateticket" method="post"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">

                        <tr height="50">
                            <td colspan="2" class="fontkink2" style="padding-left:0px;"><div class="fontpink2"> <b>Update Order !</b></div></td>

                        </tr>
                        <tr height="30">
                            <td  class="fontkink1"><b>order Id:</b></td>
                            <td  class="fontkink"><?php echo $oid; ?></td>
                        </tr>
                        <?php
                        $ret = mysqli_query($con, "SELECT * FROM ordertrackhistory WHERE orderId='$oid'");
                        while ($row = mysqli_fetch_array($ret)) {
                            ?>



                            <tr height="20">
                                <td class="fontkink1" ><b>At Date:</b></td>
                                <td  class="fontkink"><?php echo $row['postingDate']; ?></td>
                            </tr>
                            <tr height="20">
                                <td  class="fontkink1"><b>Status:</b></td>
                                <td  class="fontkink"><?php echo $row['status']; ?></td>
                            </tr>
                            <tr height="20">
                                <td  class="fontkink1"><b>Remark:</b></td>
                                <td  class="fontkink"><?php echo $row['remark']; ?></td>
                            </tr>


                            <tr>
                                <td colspan="2"><hr /></td>
                            </tr>
                        <?php } ?>
                        <?php
                        $st = 'Delivered';
                        $rt = mysqli_query($con, "SELECT * FROM orders WHERE id='$oid'");
                        while ($num = mysqli_fetch_array($rt)) {
                            $currrentSt = $num['orderStatus'];
                        }
                        if ($st == $currrentSt) {
                            ?>
                            <tr><td colspan="2"><b>
                                        Product Delivered </b></td>
                            <?php } else {
                                ?>

                                <tr height="50">
                                    <td class="fontkink1">Status: </td>
                                    <td  class="fontkink"><span class="fontkink1" >
                                            <select name="status" class="fontkink" required="required" >
                                                <option value="">Select Status</option>
                                                <option value="in Process">In Process</option>
                                                <option value="Delivered">Delivered</option>
                                            </select>
                                        </span></td>
                                </tr>

                                <tr style=''>
                                    <td class="fontkink1" >Remark:</td>
                                    <td class="fontkink" align="justify" ><span class="fontkink">
                                            <textarea cols="50" rows="7" name="remark"  required="required" ></textarea>
                                        </span></td>
                                </tr>
                                <tr>
                                    <td class="fontkink1">&nbsp;</td>
                                    <td  >&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="fontkink">       </td>
                                    <td  class="fontkink"> <input type="submit" name="submit2"  value="update"   size="40" style="cursor: pointer;" /> &nbsp;&nbsp;   
                                        <input name="Submit2" type="submit" class="txtbox4" value="Close this Window " onClick="return f2();" style="cursor: pointer;"  /></td>
                                </tr>
                            <?php } ?>
                    </table>
                </form>
            </div>

        </body>
    </html>
<?php } ?>

