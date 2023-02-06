<?php

global $connection;

$userId = $_SESSION['user']['id'];

$result = $connection->execute_query("SELECT 
users.*, 
employees.*, employees.id AS employee_id 
FROM users 
JOIN employees ON employees.user_id = users.id 
WHERE users.id = ? LIMIT 1", [$userId]);

$user = $result->fetch_assoc();

$employeeId = $user['employee_id'];

?>

<div class="navbar-bg"></div>

<!-- Topbar -->

<?php topbar() ?>

<!-- Sidebar -->
<?php sidebar() ?>

<!-- Main Content -->
<div class="main-content">

    <section class="section">

        <div class="row">

            <div class="col-12">

                <div class="card card-success">
                    <div class="card-header">
                        <h4>Formulir Kehadiran</h4>
                    </div>
                    <div class="card-body">

                        <?php if (hasFlash('error')) { ?>
                            <div class="alert alert-danger">
                                <?php echo flash('error') ?>
                            </div>
                        <?php } ?>

                        <?php if (hasFlash('success')) { ?>
                            <div class="alert alert-success">
                                <?php echo flash('success') ?>
                            </div>
                        <?php } ?>

                        <form action="<?php echo url('actions/presences/store') ?>" class="needs-validation row" novalidate="" method="POST">

                            <input type="hidden" name="employee_id" value="<?php echo $employeeId ?>">

                            <div class="form-group col-12">
                                <label>Kondisi Anda</label>
                                <select class="form-control selectric" name="status" required>
                                    <option selected disabled>Pilih kondisi anda</option>
                                    <option value="Present">Hadir</option>
                                    <option value="Permission">Izin</option>
                                    <option value="Sick">Sakit</option>
                                </select>
                                <div class="invalid-feedback">
                                    Silahkan isi pilih shift karyawan.
                                </div>
                            </div>

                            <div class="form-group col-12">
                                <label>Keterangan ( Opsional )</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary btn-lg" tabindex="4">
                                    Kirim Kehadiran
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>

        </div>

        <div class="row">

            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h4>Riwayat Kehadiran</h4>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Employee</th>
                                        <th>Date</th>
                                        <th>Presence Time</th>
                                        <th>Late Time</th>
                                        <th colspan="2">Shift</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td rowspan="3">1</td>
                                        <th rowspan="3">Rakhi Azfa Rifansya</th>
                                        <td rowspan="3">20 July 2004</td>
                                        <td rowspan="3">08:00</td>
                                        <td rowspan="3">0 Minutes</td>
                                        <td colspan="2" class="text-center">Shift Pagi</td>
                                        <td rowspan="3">
                                            <div class="badge badge-success">Present</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Start</th>
                                        <th>08:00</th>
                                    </tr>
                                    <tr>
                                        <th>End</th>
                                        <th>10:00</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </section>

</div>