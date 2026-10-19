<?php
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

require_admin();

$controller = new ProductController();
$editBrand = null;

// Check if we are editing a brand
if (isset($_GET['edit_id']) && ctype_digit($_GET['edit_id'])) {
    $editBrand = $controller->getBrandById((int) $_GET['edit_id']);
}

$brands = $controller->getAllBrands();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Brands</title>
    <link rel="stylesheet" href="../../css/style.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 40px 20px;
            background: #f4f6f9;
            font-family: Arial, sans-serif;
            color: #333;
        }

        .brand-page {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }

        .page-heading {
            text-align: center;
            color: #EB4511;
            margin: 0 0 30px;
            font-size: 32px;
        }

        .brand-panel {
            background: #ffffff;
            padding: 28px;
            margin-bottom: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .brand-panel h2 {
            margin: 0 0 22px;
            color: #333;
            font-size: 22px;
            text-align: center;
        }

        .message {
            padding: 13px 16px;
            margin-bottom: 20px;
            border-radius: 7px;
            text-align: center;
            overflow-wrap: anywhere;
        }

        .success-message {
            background: #e7f7ed;
            color: #176b35;
            border: 1px solid #b7e4c7;
        }

        .error-message {
            background: #fdeaea;
            color: #a12626;
            border: 1px solid #f5c2c2;
        }

        .brand-form {
            max-width: 550px;
            margin: 0 auto;
        }

        .brand-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .brand-form input[type="text"] {
            display: block;
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 18px;
            border: 1px solid #ccd2d9;
            border-radius: 7px;
            font-size: 16px;
            background: #fff;
        }

        .brand-form input[type="text"]:focus {
            outline: none;
            border-color: #EB4511;
            box-shadow: 0 0 0 3px rgba(235, 69, 17, 0.12);
        }

        .button-row {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn {
            display: inline-block;
            padding: 11px 22px;
            border: none;
            border-radius: 7px;
            text-decoration: none;
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: #EB4511;
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #c9380c;
        }

        .btn-secondary {
            background: #e8ebef;
            color: #333333;
        }

        .btn-secondary:hover {
            background: #d6dbe1;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .brand-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .brand-table th,
        .brand-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .brand-table th {
            background: #EB4511;
            color: #ffffff;
            font-weight: bold;
        }

        .brand-table th:first-child {
            border-radius: 7px 0 0 0;
        }

        .brand-table th:last-child {
            border-radius: 0 7px 0 0;
        }

        .brand-table tbody tr:hover {
            background: #fff5f0;
        }

        .brand-table .empty-row {
            text-align: center;
            padding: 25px;
            color: #666;
        }

        .edit-link {
            display: inline-block;
            color: #c9380c;
            font-weight: bold;
            text-decoration: none;
            padding: 6px 12px;
            border: 1px solid #EB4511;
            border-radius: 5px;
        }

        .edit-link:hover {
            background: #EB4511;
            color: #ffffff;
        }

        .home-link-container {
            text-align: center;
            margin-top: 30px;
        }

        .back-home-btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            padding: 13px 25px;
            background: #FFCA3A;
            color: #292929;
            border: 2px solid #FFCA3A;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.10);
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .back-home-btn:hover {
            background: #f0b91f;
            border-color: #f0b91f;
            transform: translateY(-2px);
        }

        @media (max-width: 600px) {
            body {
                padding: 25px 12px;
            }

            .page-heading {
                font-size: 26px;
            }

            .brand-panel {
                padding: 18px;
            }

            .brand-table th,
            .brand-table td {
                padding: 11px 9px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="brand-page">

        <h1 class="page-heading">Manage Brands</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="message success-message">
                <?php echo e($_SESSION['success']); ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error-message">
                <?php echo e($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <div class="message error-message">
                    <?php echo e($error); ?>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <section class="brand-panel">
            <h2><?php echo $editBrand ? 'Edit Brand' : 'Add New Brand'; ?></h2>

            <?php if ($editBrand): ?>
                <form class="brand-form"
                      action="../../actions/update_brand_action.php"
                      method="POST">

                    <input
                        type="hidden"
                        name="brand_id"
                        value="<?php echo e($editBrand['brand_id']); ?>"
                    >

                    <label for="brand_name">Brand Name</label>
                    <input
                        type="text"
                        id="brand_name"
                        name="brand_name"
                        value="<?php echo e($editBrand['brand_name']); ?>"
                        placeholder="Enter brand name"
                        required
                    >

                    <div class="button-row">
                        <button class="btn btn-primary" type="submit">
                            Update Brand
                        </button>

                        <a class="btn btn-secondary" href="brand.php">
                            Cancel
                        </a>
                    </div>
                </form>

            <?php else: ?>
                <form class="brand-form"
                      action="../../actions/add_brand_action.php"
                      method="POST">

                    <label for="brand_name">Brand Name</label>
                    <input
                        type="text"
                        id="brand_name"
                        name="brand_name"
                        placeholder="Enter brand name"
                        required
                    >

                    <div class="button-row">
                        <button class="btn btn-primary" type="submit">
                            Add Brand
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </section>

        <section class="brand-panel">
            <h2>All Brands</h2>

            <div class="table-wrapper">
                <table class="brand-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Brand Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($brands)): ?>
                            <tr>
                                <td class="empty-row" colspan="3">
                                    No brands found. Add your first brand above.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($brands as $brand): ?>
                                <tr>
                                    <td>
                                        <?php echo e($brand['brand_id']); ?>
                                    </td>

                                    <td>
                                        <?php echo e($brand['brand_name']); ?>
                                    </td>

                                    <td>
                                        <a
                                            class="edit-link"
                                            href="brand.php?edit_id=<?php echo (int) $brand['brand_id']; ?>"
                                        >
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <div class="home-link-container">
            <a class="back-home-btn" href="../../index.php">
                &#8592; Back to Home
            </a>
        </div>

    </div>
</body>
</html>