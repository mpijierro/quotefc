<form name="form_fc" action="" method="post" class="form">

    <div class="titulo">
        <span class="script">Quote FC</span>
        ¿Te crees que has escrito un post mítico?...¿quieres saber si te han citado en alguna parte de un hilo?...Tus opiniones son tomadas
        en cuenta o por el contrario eres considerado un troll más y no pintas nada en Forocoches.
    </div>

    <div class="campo largo">
        <span>HILO</span>
        <input type="text" name="thread" class="largo" value=""/>
        <img src='ayuda.png' title="Copia aquí la dirección completa del hilo de forocoches en el cual quieras buscar al usuario citado"
             alt="Copia aquí la direcciín completa del hilo de forocoches en el cual quieras buscar al usuario citado"
        />
    </div>
    <div class="campo">
        <span>USUARIO BúSQUEDA</span>
        <input type="text" class="medio" name="user_searched" value=""/>
        <img src='ayuda.png' title="Indica el usuario que quieras buscar para ver si ha sido citado"
             alt="Indica el usuario que quieras buscar para ver si ha sido citado"
        />
    </div>
    <div class="campo corto">
        <span>PÁGINA INICIO</span>
        <input type="text" class="corto" name="first_page" maxlength="2" value=""/>
        <img src='ayuda.png' title="Indica la página de inicio para iniciar la búsqueda"
             alt="Indica la página de inicio para iniciar la búsqueda"
        />
    </div>
    <div class="campo corto">
        <span>PÁGINA FINAL</span>
        <input type="text" class="corto" name="last_page" maxlength="2" value=""/>
        <img src='ayuda.png' title="Indica la página de final. En cualquier caso, la búsqueda funcionará en 10 páginas como máximo."
             alt="Indica la página de final. En cualquier caso, la búsqueda funcionará en 10 páginas como máximo."
        />
    </div>

    <div class="usuario_registrado">
        <div class="div_aviso">
            <p>El <strong>uso de estos campos privados es opcional</strong> para los hilos públicos, pero necesarios para poder rastrear citas en hilos +18,+HD,+PRV...</p>
        </div>
        <div class="campo medio">
            <span class="privado">USUARIO FC</span>
            <input type="text" class="medio" name="user" value=""/>
            <img src='ayuda.png' title="Indica tu usuario de forocoches" alt="Indica tu usuario de forocoches"
            />
        </div>
        <div class="campo medio">
            <span class="privado">CLAVE FC</span>
            <input type="password" class="medio" name="password" value=""/>
            <img src='ayuda.png' title="Indica tu clave forocochera" alt="Indica tu clave forocochera"
            />
        </div>
    </div>
    <div class="campo boton">
        <input type="submit" value="Buscar"/>
    </div>
</form>
</br>

<form name="form_fc" action="" method="post" class="form">
    <p><strong>RASTREO DE MÚLTIPLES HILOS</strong></p>

    <div class="usuario_registrado rastreo_multiple">
        <div class="div_aviso">
            <p>Es posible rastrear múltiples hilos para así no tener que ir buscando uno por uno. Generará un archivo llamado por defecto 'hilos.txt' para ir almacenando los hilos a revisar.
                Para ello, es necesario rellenar cada fila del siguiente textareas siguiente el formmato sigueinte:</p>
            <p><strong>URL del hilo<?php echo SEPARADOR ?>usuario<?php echo SEPARADOR ?>inicio<?php echo SEPARADOR ?>final<?php echo SEPARADOR ?>usuario_acceso<?php echo SEPARADOR ?>
                    clave_acceso</strong>
                <br/><br/>
                Ejemplo:<br/>
                http://www.forocoches.com/foro/showthread.php?t=3191579<?php echo SEPARADOR ?>hilario<?php echo SEPARADOR ?>1<?php echo SEPARADOR ?>10<?php echo SEPARADOR ?>
                hilario<?php echo SEPARADOR ?>athenea
            </p>

            <p>Los <strong>datos privados no se guardan por defecto</strong> en ningún fichero. Si quieres que queden almacenados deberás cambiar en el fichero <i>constantes.php</i> la constante <i>SAVE_PRIVATE_DATA</i>
                con valor TRUE</p>
        </div>

        <textarea name="threads" rows="10" cols="100" style="font-size:10px;"><?php echo $threadsFromFile ?></textarea>
        <br/>
        <div class="campo boton">
            <input type="submit" value="Añadir y buscar"/>
        </div>
    </div>
</form>


<form name="aviso" action="" method="post" class="form">
    <p><strong>INFO</strong></p>

    <div class="usuario_registrado aviso_cop">
        <div class="div_aviso">
            <p>La finalidad y el objetivo de implementar este script ha sido meramente académica.</p>
            <p>La responsabilidad final sobre el uso de este script y sus consecuencias recaen únicamente en aquella persona que lo ejecute.</p>
            <p>Este código tiene licencia <a href="https://es.wikipedia.org/wiki/GNU_General_Public_License" target="_blank">GNU</a> por lo que
                garantiza a los usuarios finales (personas, organizaciones, compañías) la libertad de usar, estudiar, compartir (copiar) y modificar el software. Su propósito es declarar que el
                software cubierto por esta licencia es software libre y protegerlo de intentos de apropiación que restrinjan esas libertades a los usuarios.
                (según la Wikipedia :) ).
            </p>
        </div>
    </div>
</form>