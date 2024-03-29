<div class="SnContent">
    <div class="SnGrid m-grid-3 l-grid-3 col-gap">
        <div class="SnCard DashCard blue">
            <div class="SnCard-body DashCard-body">
                <div class="DashCard-icon"><i class="far fa-user"></i></div>
                <div class="DashCard-right">
                    <div class="DashCard-title">Usuarios</div>
                    <div class="DashCard-number"><?= $parameter['userCount'] ?></div>
                </div>
            </div>
        </div>
        <div class="SnCard DashCard green">
            <div class="SnCard-body DashCard-body">
                <div class="DashCard-icon"><i class="far fa-address-book"></i></div>
                <div class="DashCard-right">
                    <div class="DashCard-title">Clientes</div>
                    <div class="DashCard-number"><?= $parameter['customerCount'] ?></div>
                </div>
            </div>
        </div>
        <div class="SnCard DashCard purple">
            <div class="SnCard-body DashCard-body">
                <div class="DashCard-icon"><i class="fas fa-charging-station"></i></div>
                <div class="DashCard-right">
                    <div class="DashCard-title">Exibidoras</div>
                    <div class="DashCard-number"><?= $parameter['exhibitorCount'] ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="SnCard SnMb-3" id="filterWrapper">
        <div class="SnCard-body">
            <div class="SnGrid s-grid-2 col-gap">
                <div class="SnForm-item inner" style="margin-bottom: 0;">
                    <label for="chartStartDate" class="SnForm-label">Desde</label>
                    <input type="date" id="chartStartDate" class="SnForm-control" value="<?php echo date('Y-m-d', strtotime('-1 year')) ?>">
                </div>
                <div class="SnForm-item inner" style="margin-bottom: 0;">
                    <label for="chartEndDate" class="SnForm-label">Hasta</label>
                    <input type="date" id="chartEndDate" class="SnForm-control" value="<?php echo date('Y-m-d', strtotime('+1 day')) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="SnGrid l-grid-2 col-gap">
        <div class="SnCard">
            <div class="SnCard-body">
                <div class="SnCard-title">Pedidos</div>
                <div style="height: 320px">
                    <canvas id="ordersChart" width="320" height="320"></canvas>
                </div>
            </div>
        </div>
        <div class="SnCard">
            <div class="SnCard-body">
                <div class="SnCard-title">Entregas</div>
                <div style="height: 320px">
                    <canvas id="deliveryChart" width="320" height="320"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= URL_PATH ?>/assets/libraries/js/moment-with-locales.min.js"></script>
<script src="<?= URL_PATH ?>/assets/libraries/js/chart.min.js"></script>
<script src="<?= URL_PATH ?>/assets/build/script/admin/dashboard.js"></script>