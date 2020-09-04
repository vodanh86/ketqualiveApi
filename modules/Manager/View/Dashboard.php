<div class="pg-title dashboard-title">Thống kê</div>
<div class="pg-content dashboard-content">
    <div class="row tile_count">
        <div class="col-md-2 col-sm-4 col-12 tile_stats_count">
            <span class="count_top"><i class="fa fa-users"></i> Thành viên</span>
            <div class="count"><?php echo number_format($user['total'], 0, ',', '.') ?></div>
        </div>
            <div class="col-md-2 col-sm-4 col-12 tile_stats_count">
                <a href="user-vip">
                    <span class="count_top"><i class="fa fa-user"></i> VIP</span>
                    <div class="count"><?php echo number_format($user['vip'], 0, ',', '.') ?></div>
                </a>
            </div>

        <div class="col-md-2 col-sm-4 col-12 tile_stats_count">
            <a href="user-supervip">
                <span class="count_top"><i class="fa fa-user"></i> SUPERVIP</span>
                <div class="count"><?php echo number_format($user['supervip'], 0, ',', '.') ?></div>
            </a>
        </div>
        <div class="col-md-2 col-sm-4 col-12 tile_stats_count">
            <span class="count_top"><i class="fa fa-database"></i> COIN</span>
            <div class="count"><?php echo number_format($user['total_coin'], 0, ',', '.') ?></div>
        </div>
        <div class="col-md-2 col-sm-4 col-12 tile_stats_count">
            <a href="charge-coin">
                <span class="count_top"><i class="fa fa-database"></i> COIN đã nạp</span>
                <div class="count"><?php echo number_format($coin['charged_coin'], 0, ',', '.') ?></div>
                <span class="count_bottom">trong ngày</span>
            </a>
        </div>
        <div class="col-md-2 col-sm-4 col-12 tile_stats_count">
            <a href="consume-coin">
                <span class="count_top"><i class="fa fa-database"></i> COIN đã dùng</span>
                <div class="count"><?php echo number_format($coin['consumed_coin'], 0, ',', '.') ?></div>
                <span class="count_bottom">trong ngày</span>
            </a>
        </div>
    </div>
</div>
