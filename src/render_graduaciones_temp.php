<?php
/**
 * Lista graduaciones_temp del usuario. Espera $conexion y $id_usuario (int).
 */
if (!isset($conexion) || !isset($id_usuario)) {
    return;
}

if (!function_exists('grad_rx_fmt')) {
    function grad_rx_fmt($v)
    {
        $s = (string) $v;
        if ($s === '' || $s === '0') {
            return '—';
        }
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }

    /** Celda compacta Esf · Cil · Eje (una sola línea, sin romper números largos mal) */
    function grad_rx_cell($e1, $e2, $e3)
    {
        $a = grad_rx_fmt($e1);
        $b = grad_rx_fmt($e2);
        $c = grad_rx_fmt($e3);
        return '<div class="grad-rx-cell">'
            . '<span class="grad-rx-bit"><abbr title="Esfera" class="grad-rx-lab">Esf</abbr><span class="grad-rx-val">' . $a . '</span></span>'
            . '<span class="grad-rx-sep" aria-hidden="true">·</span>'
            . '<span class="grad-rx-bit"><abbr title="Cilindro" class="grad-rx-lab">Cil</abbr><span class="grad-rx-val">' . $b . '</span></span>'
            . '<span class="grad-rx-sep" aria-hidden="true">·</span>'
            . '<span class="grad-rx-bit"><abbr title="Eje" class="grad-rx-lab">Eje</abbr><span class="grad-rx-val">' . $c . '</span></span>'
            . '</div>';
    }
}

$id_usuario = (int) $id_usuario;
$res = mysqli_query($conexion, "SELECT * FROM graduaciones_temp WHERE id_usuario = $id_usuario ORDER BY id ASC");
$rows = [];
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $rows[] = $r;
    }
}

if (count($rows) === 0) {
    echo '<div class="grad-temp-empty text-muted mb-0"><i class="fas fa-info-circle mr-1"></i> No hay graduaciones cargadas.</div>';
    return;
}
?>
<div class="grad-temp-panel mb-4">
    <div class="grad-temp-panel-head">
        <i class="fas fa-eye mr-2"></i> Graduaciones cargadas
        <span class="grad-temp-count badge badge-light text-dark ml-2"><?php echo count($rows); ?></span>
    </div>
    <div class="table-responsive grad-temp-table-wrap">
        <table class="table table-modern table-graduaciones-temp mb-0">
            <thead>
                <tr>
                    <th scope="col" class="grad-th-rx">OD cerca</th>
                    <th scope="col" class="grad-th-rx">OI cerca</th>
                    <th scope="col" class="grad-th-narrow text-center">ADD</th>
                    <th scope="col" class="grad-th-obs">Obs.</th>
                    <th scope="col" class="grad-th-rx">OD lejos</th>
                    <th scope="col" class="grad-th-rx">OI lejos</th>
                    <th scope="col" class="text-center grad-th-actions">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row2) :
                    $idRow = (int) $row2['id'];
                    $gradJson = htmlspecialchars(json_encode($row2, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8');
                    $obsRaw = (string) $row2['obs'];
                    $obsEsc = htmlspecialchars($obsRaw, ENT_QUOTES, 'UTF-8');
                    $addDisp = grad_rx_fmt($row2['addg']);
                    ?>
                <tr class="grad-temp-row">
                    <td class="grad-td-rx"><?php echo grad_rx_cell($row2['od_c_1'], $row2['od_c_2'], $row2['od_c_3']); ?></td>
                    <td class="grad-td-rx"><?php echo grad_rx_cell($row2['oi_c_1'], $row2['oi_c_2'], $row2['oi_c_3']); ?></td>
                    <td class="text-center align-middle grad-td-add"><?php echo $addDisp === '—' ? '<span class="grad-muted">—</span>' : '<span class="grad-add-pill">' . $addDisp . '</span>'; ?></td>
                    <td class="align-middle grad-td-obs">
                        <span class="grad-obs-text" title="<?php echo $obsEsc; ?>"><?php echo $obsEsc; ?></span>
                    </td>
                    <td class="grad-td-rx"><?php echo grad_rx_cell($row2['od_l_1'], $row2['od_l_2'], $row2['od_l_3']); ?></td>
                    <td class="grad-td-rx"><?php echo grad_rx_cell($row2['oi_l_1'], $row2['oi_l_2'], $row2['oi_l_3']); ?></td>
                    <td class="text-center align-middle text-nowrap grad-td-actions">
                        <button type="button" class="btn btn-sm btn-modern btn-modern-primary btn-editar-graduacion mr-1" data-grad="<?php echo $gradJson; ?>" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-modern btn-modern-danger btn-eliminar-graduacion" data-id="<?php echo $idRow; ?>" title="Eliminar esta graduación">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
