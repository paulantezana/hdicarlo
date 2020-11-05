<div class="SnContent">
    <div class="SnGrid m-grid-3 l-grid-4 col-gap">
        <div class="SnCard DashCard">
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
                <div class="DashCard-icon"><i class="far fa-user"></i></div>
                <div class="DashCard-right">
                    <div class="DashCard-title">Clientes</div>
                    <div class="DashCard-number"><?= $parameter['customerCount'] ?></div>
                </div>
            </div>
        </div>
        <div class="SnCard DashCard purple">
            <div class="SnCard-body DashCard-body">
                <div class="DashCard-icon"><i class="far fa-user"></i></div>
                <div class="DashCard-right">
                    <div class="DashCard-title">Exibidoras</div>
                    <div class="DashCard-number"><?= $parameter['exhibitorCount'] ?></div>
                </div>
            </div>
        </div>
        <div class="SnCard DashCard blue">
            <div class="SnCard-body DashCard-body">
                <div class="DashCard-icon"><i class="far fa-user"></i></div>
                <div class="DashCard-right">
                    <div class="DashCard-title">-</div>
                    <div class="DashCard-number">-</div>
                </div>
            </div>
        </div>
    </div>

    <div class="SnGrid l-grid-2 col-gap">
        <div class="SnCard">
            <div class="SnCard-body">
                <div class="SnCard-title">Pedidos</div>
            </div>
        </div>
        <div class="SnCard">
            <div class="SnCard-body">
                <div class="SnCard-title">Entregas</div>
            </div>
        </div>
    </div>
</div>

<script src="<?= URL_PATH ?>/assets/script/dashboard.js"></script>