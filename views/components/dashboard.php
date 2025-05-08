<div class="mx-4 mt-0">
    <div class="row mb-4">
        <div class="col-lg-12 col-md-6">
            <div class="d-flex lign-items-center">
                <div class="card-db bg-white p-2 me-3 w-100">
                    <div class="d-flex justify-content-between align-items-center me-2">
                        <div class="px-2 py-2">
                            <h3 class="overview-db text-black-50 lh-1">Traffic</h3>
                            <strong id="valTraffic" class="title-db lh-1"><?= $traffic ?></strong>
                        </div>
                        <div class="d-flex justify-content-center align-items-center rounded-circle bg-traffic badge">
                            <i class="fa-solid fa-chart-column fa-2xl" style="color: #ffffff;"></i>
                        </div>
                    </div>
                </div>
                <div class="card-db bg-white p-2 me-3 w-100">
                    <div class="d-flex justify-content-between align-items-center me-2">
                        <div class="px-2 py-2">
                            <h3 class="overview-db text-black-50 lh-1">New Files</h3>
                            <strong id="valFile" class="title-db lh-1"><?= $newFile ?></strong>
                        </div>
                        <div class="d-flex justify-content-center align-items-center rounded-circle bg-file badge">
                            <i class="fa-solid fa-file fa-2xl" style="color: #ffffff;"></i>
                        </div>
                    </div>
                </div>
                <div class="card-db bg-white p-2 me-3 w-100">
                    <div class="d-flex justify-content-between align-items-center me-2">
                        <div class="px-2 py-2">
                            <h3 class="overview-db text-black-50 lh-1">Today / Total Downloads</h3>
                            <strong id="valDownload" class="title-db lh-1"><?= $todayDownload ?>/<?= $countDownload ?></strong>
                        </div>
                        <div class="d-flex justify-content-center align-items-center rounded-circle bg-download badge">
                            <i class="fa-solid fa-download fa-2xl" style="color: #ffffff;"></i>
                        </div>
                    </div>
                </div>
                <div class="card-db bg-white p-2 w-100">
                    <div class="d-flex justify-content-between align-items-center me-2">
                        <div class="px-2 py-2">
                            <h3 class="overview-db text-black-50 lh-1">Total Visitors</h3>
                            <strong id="valVisitor" class="title-db lh-1"><?= $countVisitor ?></strong>
                        </div>
                        <div class="d-flex justify-content-center align-items-center rounded-circle bg-visitor badge">
                            <i class="fa-solid fa-user fa-2xl" style="color: #ffffff;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8 col-md-6">
            <div class="px-4 py-4 card-dashboard">
                <div class="d-flex justify-content-between align-items-end">
                    <h3 class="overview-db text-black-50">Overview</h3>
                    <?php if ($countDownload != 0) { ?>
                        <div>
                            <select class="overview-db form-select text-black text-end dropdown-filter m-0" id="selectDate" style="font-size:12px;">
                                <option value="allTime">All Time</option>
                                <option value="month">This Month</option>
                                <option value="year">This Year</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                    <?php }; ?>
                </div>
                <div class="d-flex justify-content-between">
                    <h3 class="title-db m-0">
                        Graph of Total Downloads 
                        <?php if ($countDownload != 0) { ?>
                            <span>per 
                                <select class="title-db mb-3 dropdown-filter" id="groupBy">
                                    <option value="byDate">Date</option>
                                    <option value="byMonth">Month</option>
                                </select>
                            </span>
                        <?php }; ?>
                    </h3>
                    <?php if ($countDownload != 0) { ?>
                        <p id="dateRangeText" class="fst-italic me-3" style="font-size: 12px; color: rgba(0,0,0,0.7);"></p>
                    <?php }; ?>
                </div>
                <?php if ($countDownload != 0) { ?>
                    <canvas id="graphDate"></canvas>
                <?php } else { ?>
                    <div class="d-flex flex-column align-items-center justify-content-end m-5">
                        <img src="../assets/empty-state-doc.png" style="height: 40%;">
                        <p class="text-black-50 fw-semibold fs-5 px-3 my-2 align-text-center">
                            There are no files downloaded by the user yet
                        </p>
                    </div>
                <?php }; ?>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="px-4 py-4 card-dashboard h-100 d-flex flex-column flex-grow-1">
                <h3 class="overview-db text-black-50 mb-1"><?= $currentDate ?></h3>
                <div class="d-flex mb-3 justify-content-between">
                    <h3 class="title-db m-0 d-flex align-items-center">Latest Visitor</h3>
                    <h4 class="overview-db fst-italic m-0 d-flex align-items-center">Last Active:</h4>
                </div>
                <div class="d-flex flex-column flex-grow-1">
                    <?php if (empty($onlineVisitors) && empty($pastVisitors)) { ?>
                        <div class="d-flex flex-column align-items-center my-auto">
                            <img src="../assets/empty-state-doc.png" style="width: 25%;">
                            <p class="text-black-50 fw-semibold px-3 my-2 text-center" style="font-size:14px;">
                                No recent visitors today <br>(<?= $currentDate ?>)
                            </p>
                        </div>
                    <?php }; ?>
                    <?php foreach ($onlineVisitors as $visitor) { ?>
                        <div class="d-flex justify-content-between">
                            <p class="text-black-50 fw-semibold fs-6 px-2 mb-2">
                                <?= $visitor['full_name'] ?> <?= $visitor['npk_user'] ?>
                            </p>
                            <p class="text-online fw-bold fs-6 px-2 mb-2">
                                online
                            </p>
                        </div>
                    <?php }; ?>
                    <?php foreach ($pastVisitors as $visitor) { ?>
                        <div class="d-flex justify-content-between">
                            <p class="text-black-50 fw-semibold fs-6 px-2 mb-2">
                                <?= $visitor['full_name'] ?> <?= $visitor['npk_user'] ?>
                            </p>
                            <p class="text-black-50 fw-bold fs-6 px-2 mb-2">
                                <?= $visitor['date_logout'] ?>
                            </p>
                        </div>
                    <?php }; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 col-md-12 mb-lg-0 mb-3">
            <div class="px-4 py-4 card-dashboard">
                <h3 class="overview-db text-black-50">Overview</h3>
                <h3 class="title-db mb-2">Top 10 Files Downloaded by Users</h3>
                <?php if ($countDownload != 0) { ?>
                    <canvas id="graphFile"></canvas>
                <?php } else { ?>
                    <div class="d-flex flex-column align-items-center justify-content-end m-5">
                        <img src="../assets/empty-state-doc.png" style="height: 40%;">
                        <p class="text-black-50 fw-semibold px-3 my-2 align-text-center" style="font-size:14px;">
                            There are no files downloaded by the user yet
                        </p>
                    </div>
                <?php }; ?>
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <div class="px-4 py-4 card-dashboard">
                <h3 class="overview-db text-black-50">Overview</h3>
                <h3 class="title-db mb-2">Graph of Total Downloads by User</h3>
                <?php if ($countDownload != 0) { ?>
                    <canvas id="graphUser"></canvas>
                <?php } else { ?>
                    <div class="d-flex flex-column align-items-center justify-content-end m-5">
                        <img src="../assets/empty-state-doc.png" style="height: 40%;">
                        <p class="text-black-50 fw-semibold fs-5 px-3 my-2 align-text-center">
                            There are no files downloaded by the user yet
                        </p>
                    </div>
                <?php }; ?>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-12 col-md-12 mb-lg-0 mb-3">
            <div class="px-4 py-4 card-dashboard">
                <h3 class="overview-db text-black-50">Overview</h3>
                <h3 class="title-db mb-3">Latest Downloads</h3>
                <table class="table table-stripped">
                    <thead>
                        <tr>
                            <th scope="col" style="text-align:center; font-size:14px">File Name</th>
                            <th scope="col" style="text-align:center; font-size:14px">Downloaded By</th>
                            <th scope="col" style="text-align:center; font-size:14px">Position</th>
                            <th scope="col" style="text-align:center; font-size:14px">File Size</th>
                            <th scope="col" style="text-align:center; font-size:14px">Date Downloaded</th>
                        </tr>
                    </thead>
                    <tbody id="logTable">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="wrapper"></div>

<script>
    $(document).ready(function() {
        setTimeout(function() {
            document.querySelector('.wrapper').classList.add('animate');
        }, 100);
        
        <?php if ($countDownload != 0) { ?>
            fetchData();
            fetchDataAndCreateChart();
            getDashboardLogDownloads();
            $('#groupBy').on('change', updateOption);
            $('#selectDate').on('change', updateOption);
        <?php }; ?>
    });
</script>