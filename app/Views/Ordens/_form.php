<div class="user-block">

    <div class="form-row mb-4">

        <div class="col-md-12">

            <?php if ($ordem->id === null) : ?>

                <div class="contributions">

                    Ordem Aberta por: <?php echo usuario_logado()->nome; ?>

                </div>

            <?php else : ?>

                <div class="contributions">

                    Ordem Aberta por: <?php echo esc($ordem->usuario_abertura); ?>

                </div>

                <?php if ($ordem->usuario_responsavel !== null) : ?>

                    <p class="contributions mt-0">Técnico responsável: <?php echo esc($ordem->usuario_responsavel); ?></p>

                <?php endif; ?>

            <?php endif; ?>


        </div>

    </div>

    <?php if ($ordem->id === null) : ?>

        <div class="form-group">
            <label class="form-control-label">Escolha o Cliente</label>

            <select name="cliente_id" class="selectize">

                <option value="">Digite o nome do Cliente ou CPF</option>

            </select>

        </div>


    <?php else : ?>

        <div class="form-group">
            <label class="form-control-label">Cliente</label>
            <a tabindex="0" style="text-decoration: none;" role="button" data-toggle="popover" data-trigger="focus" title="Importante" data-content="Não é permitido editar o Cliente da Ordem de serviço!">
                <i class="fa fa-question-circle fa-lg text-info"></i>
            </a>
            <input type="text" class="form-control" disabled readonly value="<?php echo esc($ordem->nome); ?>">
        </div>


    <?php endif; ?>

    <div class="form-group">
        <label class="form-control-label">Equipamento</label>
        <input type="text" name="equipamento" placeholder="Descreva o equipamento" class="form-control" value="<?php echo esc($ordem->equipamento); ?>">
    </div>

    <div class="form-group">
        <label class="form-control-label">Defeitos do Equipamento</label>
        <textarea class="form-control" name="defeito" placeholder="Descreva os defeitos do equipamento"><?php echo esc($ordem->defeito); ?></textarea>
    </div>

    <div class="form-group">
        <label class="form-control-label">Observações da Ordem de Serviço</label>
        <textarea class="form-control" name="observacoes" placeholder="Informe as observações"><?php echo esc($ordem->observacoes); ?></textarea>
    </div>

    <!-- Só exibe o imput do parecer na edição -->
    
    <?php if ($ordem->id) : ?>

        <div class="form-group">
            <label class="form-control-label">Parecer Técnico</label>
            <textarea class="form-control" name="parecer_tecnico" placeholder="Informe o parecer técnico"><?php echo esc($ordem->parecer_tecnico); ?></textarea>
        </div>

    <?php endif; ?>
    
</div>