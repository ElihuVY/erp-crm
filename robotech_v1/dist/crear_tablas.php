<?php
function crearTablas($conn) {
    $sql = "
    -- Tabla clientes
    CREATE TABLE IF NOT EXISTS `clientes` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `nombre` varchar(100) NOT NULL,
      `email` varchar(100) DEFAULT NULL,
      `telefono` varchar(20) DEFAULT NULL,
      `empresa` varchar(100) DEFAULT NULL,
      `direccion` text DEFAULT NULL,
      `notas` text DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `favorito` tinyint(1) DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    -- Tabla facturas
    CREATE TABLE IF NOT EXISTS `facturas` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `cliente_id` int(11) NOT NULL,
      `organizacion_id` int(11) NOT NULL,
      `presupuesto_id` int(11) DEFAULT NULL,
      `tipo` enum('unica','recurrente') NOT NULL,
      `estado` enum('borrador','emitida','pagada','vencida') NOT NULL,
      `fecha_emision` date NOT NULL,
      `fecha_vencimiento` date DEFAULT NULL,
      `total` decimal(10,2) NOT NULL,
      `periodo_recurrente` enum('mensual','trimestral','anual') DEFAULT NULL,
      `recurrente_hasta` date DEFAULT NULL,
      `iva` decimal(5,2) DEFAULT 21.00,
      `irpf` decimal(5,2) DEFAULT 0.00,
      `impuestos_extra` text DEFAULT NULL,
      `recurrente_id` int(11) DEFAULT NULL,
      `notas` text DEFAULT NULL,
      `serie_id` int(11) DEFAULT NULL,
      `numero_serie` int(11) DEFAULT NULL,
      `numero_factura` varchar(50) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    -- Tabla factura_items
    CREATE TABLE IF NOT EXISTS `factura_items` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `factura_id` int(11) NOT NULL,
      `producto_id` int(11) DEFAULT NULL,
      `descripcion` text NOT NULL,
      `cantidad` decimal(10,2) NOT NULL,
      `precio_unitario` decimal(10,2) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


    CREATE TABLE IF NOT EXISTS `organizaciones` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `nombre` varchar(100) NOT NULL,
        `cif` varchar(20) DEFAULT NULL,
        `direccion` text DEFAULT NULL,
        `logo` varchar(255) DEFAULT NULL,
        `email` varchar(100) DEFAULT NULL,
        `telefono` varchar(50) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    CREATE TABLE IF NOT EXISTS `org_series` (
        `organizacion_id` int(11) NOT NULL,
        `serie_id` int(11) NOT NULL,
        PRIMARY KEY (`organizacion_id`,`serie_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    CREATE TABLE IF NOT EXISTS `presupuestos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `cliente_id` int(11) NOT NULL,
    `estado` enum('borrador','enviado','aceptado','rechazado') NOT NULL,
    `fecha_creacion` date NOT NULL,
    `fecha_validez` date DEFAULT NULL,
    `importe` decimal(10,2) NOT NULL,
    `convertido` tinyint(1) NOT NULL DEFAULT 0,
    `factura_id` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


    CREATE TABLE IF NOT EXISTS `productos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `organizacion_id` int(11) NOT NULL,
    `nombre` varchar(100) NOT NULL,
    `descripcion` text DEFAULT NULL,
    `precio` decimal(10,2) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `iva` decimal(5,2) DEFAULT 21.00,
    `tipo` enum('producto','servicio','contenido_digital') NOT NULL DEFAULT 'producto',
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


    CREATE TABLE IF NOT EXISTS `proyectos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `cliente_id` int(11) NOT NULL,
    `presupuesto_id` int(11) DEFAULT NULL,
    `nombre` varchar(100) NOT NULL,
    `descripcion` text DEFAULT NULL,
    `estado` enum('planificado','en curso','completado','cancelado') NOT NULL,
    `fecha_inicio` date DEFAULT NULL,
    `fecha_fin` date DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


    CREATE TABLE IF NOT EXISTS `series` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `organizacion_id` int(11) NOT NULL,
    `nombre` varchar(50) NOT NULL,
    `numero_inicial` int(11) DEFAULT 1,
    `numeracion_manual` tinyint(1) DEFAULT 0,
    `reiniciar_anual` tinyint(1) DEFAULT 0,
    `rectificativa` tinyint(1) DEFAULT 0,
    `visible` tinyint(1) DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `prefijo` varchar(20) DEFAULT '',
    `iva` decimal(5,2) DEFAULT 21.00,
    `irpf` decimal(5,2) DEFAULT 0.00,
    `impuestos_extra` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


    CREATE TABLE IF NOT EXISTS `tareas` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `proyecto_id` int(11) NOT NULL,
    `titulo` varchar(100) NOT NULL,
    `descripcion` text DEFAULT NULL,
    `estado` enum('pendiente','en curso','finalizada') NOT NULL DEFAULT 'pendiente',
    `responsable` varchar(100) DEFAULT NULL,
    `prioridad` enum('baja','media','alta') DEFAULT NULL,
    `comentario` text DEFAULT NULL,
    `fecha_inicio` date DEFAULT NULL,
    `fecha_limite` date DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `tarea_padre_id` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


    CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_nif` varchar(20) NOT NULL,
    `nombre` varchar(100) NOT NULL,
    `email` varchar(100) DEFAULT NULL,
    `telefono` varchar(20) DEFAULT NULL,
    `direccion` text DEFAULT NULL,
    `empresa` varchar(100) DEFAULT NULL,
    `notas` text DEFAULT NULL,
    `password` varchar(255) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


    CREATE TABLE IF NOT EXISTS `usuario_organizacion` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `usuario_id` int(11) DEFAULT NULL,
    `organizacion_id` int(11) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    
    --
    -- Indices de la tabla `clientes`
    --
    -- ALTER TABLE `clientes`
    -- ADD PRIMARY KEY (`id`);
    --
    -- Indices de la tabla `facturas`
    --
    -- ALTER TABLE `facturas`
    -- ADD PRIMARY KEY (`id`),
    ALTER TABLE `facturas`
    ADD KEY `fk_factura_cliente` (`cliente_id`),
    ADD KEY `fk_factura_presupuesto` (`presupuesto_id`),
    ADD KEY `fk_facturas_series` (`serie_id`);

    --
    -- Indices de la tabla `factura_items`
    --
    -- ALTER TABLE `factura_items`
    -- ADD PRIMARY KEY (`id`),
    ALTER TABLE `factura_items`
    ADD KEY `factura_id` (`factura_id`),
    ADD KEY `producto_id` (`producto_id`);

    --
    -- Indices de la tabla `organizaciones`
    --
    -- ALTER TABLE `organizaciones`
    -- ADD PRIMARY KEY (`id`);

    --
    -- Indices de la tabla `org_series`
    --
    -- ALTER TABLE `org_series`
    -- ADD PRIMARY KEY (`organizacion_id`,`serie_id`),
    ALTER TABLE `org_series`
    ADD KEY `serie_id` (`serie_id`);

    --
    -- Indices de la tabla `presupuestos`
    --
    -- ALTER TABLE `presupuestos`
    -- ADD PRIMARY KEY (`id`),
    ALTER TABLE `presupuestos`
    ADD KEY `fk_presupuesto_cliente` (`cliente_id`);

    --
    -- Indices de la tabla `productos`
    --
    -- ALTER TABLE `productos`
    -- ADD PRIMARY KEY (`id`),
    ALTER TABLE `productos`
    ADD KEY `organizacion_id` (`organizacion_id`);

    --
    -- Indices de la tabla `proyectos`
    --
    -- ALTER TABLE `proyectos`
    -- ADD PRIMARY KEY (`id`),
    ALTER TABLE `proyectos`
    ADD KEY `cliente_id` (`cliente_id`),
    ADD KEY `presupuesto_id` (`presupuesto_id`);

    --
    -- Indices de la tabla `series`
    --
    -- ALTER TABLE `series`
    -- ADD PRIMARY KEY (`id`),
    ALTER TABLE `series`
    ADD KEY `organizacion_id` (`organizacion_id`);

    --
    -- Indices de la tabla `tareas`
    --
    -- ALTER TABLE `tareas`
    -- ADD PRIMARY KEY (`id`),
    ALTER TABLE `tareas`
    ADD KEY `fk_tarea_proyecto` (`proyecto_id`);

    --
    -- Indices de la tabla `usuarios`
    --
    -- ALTER TABLE `usuarios`
    -- ADD PRIMARY KEY (`id`);

    --
    -- Indices de la tabla `usuario_organizacion`
    --
    -- ALTER TABLE `usuario_organizacion`
    -- ADD PRIMARY KEY (`id`),
    ALTER TABLE `usuario_organizacion`
    ADD KEY `fk_usuario_id` (`usuario_id`),
    ADD KEY `fk_organizacion_id` (`organizacion_id`);

    --
    -- Restricciones para tablas volcadas
    --

    --
    -- Filtros para la tabla `facturas`
    --
    ALTER TABLE `facturas`
    ADD CONSTRAINT `fk_factura_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
    ADD CONSTRAINT `fk_factura_presupuesto` FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuestos` (`id`) ON DELETE SET NULL,
    ADD CONSTRAINT `fk_facturas_series` FOREIGN KEY (`serie_id`) REFERENCES `series` (`id`);

    --
    -- Filtros para la tabla `factura_items`
    --
    ALTER TABLE `factura_items`
    ADD CONSTRAINT `factura_items_ibfk_1` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `factura_items_ibfk_3` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

    --
    -- Filtros para la tabla `org_series`
    --
    ALTER TABLE `org_series`
    ADD CONSTRAINT `org_series_ibfk_1` FOREIGN KEY (`organizacion_id`) REFERENCES `organizaciones` (`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `org_series_ibfk_2` FOREIGN KEY (`serie_id`) REFERENCES `series` (`id`) ON DELETE CASCADE;

    --
    -- Filtros para la tabla `presupuestos`
    --
    ALTER TABLE `presupuestos`
    ADD CONSTRAINT `fk_presupuesto_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);

    --
    -- Filtros para la tabla `productos`
    --
    ALTER TABLE `productos`
    ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`organizacion_id`) REFERENCES `organizaciones` (`id`) ON DELETE CASCADE;

    --
    -- Filtros para la tabla `proyectos`
    --
    ALTER TABLE `proyectos`
    ADD CONSTRAINT `proyectos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `proyectos_ibfk_2` FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuestos` (`id`) ON DELETE SET NULL;

    --
    -- Filtros para la tabla `series`
    --
    ALTER TABLE `series`
    ADD CONSTRAINT `series_ibfk_1` FOREIGN KEY (`organizacion_id`) REFERENCES `organizaciones` (`id`);

    --
    -- Filtros para la tabla `tareas`
    --
    ALTER TABLE `tareas`
    ADD CONSTRAINT `fk_tarea_proyecto` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE;

    --
    -- Filtros para la tabla `usuario_organizacion`
    --
    ALTER TABLE `usuario_organizacion`
    ADD CONSTRAINT `fk_organizacion_id` FOREIGN KEY (`organizacion_id`) REFERENCES `organizaciones` (`id`),
    ADD CONSTRAINT `fk_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
    COMMIT;
    ";

    if ($conn->multi_query($sql)) {
        while ($conn->more_results() && $conn->next_result()) {} // Limpiar resultados
        echo "<script>console.log('Tablas creadas o verificadas correctamente');</script>";
    } else {
        die("Error al crear tablas: " . $conn->error);
    }
}
?>