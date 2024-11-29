USE RichMond_db;

INSERT INTO categories (category_name, category_description, category_status) VALUES
('jeans', 'Prenda básica y versátil en diferentes estilos y colores. Cómodos y duraderos para cualquier ocasión.', 'active'), 
('pantalones', 'Prendas que combinan estilo y comodidad. Disponibles en modelos formales e informales.', 'active'),
('camisetas', 'Camisetas cómodas y casuales con gran variedad de colores, estampados y materiales.', 'active'),
('camisas', 'Camisas básicas ideales para ocasiones formales e informales, con diferentes cortes y tejidos.', 'active'),
('sudaderas', 'Prendas cómodas y abrigadas, ideales para un look casual o deportivo.', 'active'),
('accesorios', 'Artículos como bolsos, gafas, relojes y más para complementar cualquier estilo.', 'inactive');


INSERT INTO products (product_name, product_description, stock, price, image_url, category_id) VALUES
-- Categoría: Jeans (category_id = 1)
('Jean bota recta', 'Jean de corte recto, diseñado para brindar comodidad y estilo. Confeccionado en tela de mezclilla de alta calidad, con un acabado moderno y fresco. Su ajuste en la cintura y corte recto desde la cadera hasta el tobillo lo hacen adecuado para cualquier tipo de cuerpo. Ideal para combinar con camisetas o camisas para un look casual y sofisticado.', 30, 79900, 'imagen.webp', 1),

('Jean slim fit', 'Jean de corte slim fit, perfecto para quienes buscan un ajuste más ceñido al cuerpo. Hecho de mezclilla de alta calidad, cómodo y resistente. Ideal para combinar con una camiseta básica o una camisa para un look más estilizado.', 40, 95000, 'producto.PNG', 1),

('Jean bootcut', 'Jean con corte bootcut, ligeramente acampanado en la parte inferior para ofrecer un ajuste cómodo en las piernas. Hecho con mezclilla flexible y de alta calidad, es ideal para quienes buscan un estilo relajado pero elegante.', 25, 85000, 'imagen.webp', 1),

('Jean oscuro', 'Jean en color azul oscuro, ideal para quienes buscan una prenda versátil que se adapte a ocasiones más formales o casuales. Corte regular, cómodo y resistente.', 20, 89000, 'producto.PNG', 1),

('Jean rasgado', 'Jean con detalles rasgados en las piernas, para un estilo más moderno y urbano. De corte slim fit y fabricado en mezclilla de alta calidad, ideal para un look casual y desenfadado.', 35, 95000, 'imagen.webp', 1),

('Jean de tiro alto', 'Jean de tiro alto con un corte recto, ideal para quienes buscan comodidad y estilo. Hecho con mezclilla de algodón elástico, perfecto para un look retro y moderno al mismo tiempo.', 50, 79900, 'producto.PNG', 1),

-- Categoría: Pantalones (category_id = 2)
('Pantalón de vestir', 'Pantalón de vestir en color gris, ideal para ocasiones formales. Hecho con material suave y ligero, proporcionando comodidad y elegancia. Perfecto para combinar con camisas o blazers.', 40, 120000, 'imagen.webp', 2),

('Pantalón chino', 'Pantalón chino en color beige, casual y cómodo. Hecho con algodón de alta calidad, ideal para el día a día o un look más relajado. Perfecto para combinar con camisetas o camisas.', 60, 98000, 'producto.PNG', 2),

('Pantalón slim fit', 'Pantalón de corte slim fit en color negro, ideal para un look más ajustado y estilizado. Hecho con material elástico para mayor comodidad y libertad de movimiento.', 45, 95000, 'imagen.webp', 2),

('Pantalón cargo', 'Pantalón estilo cargo con múltiples bolsillos, en color verde oliva. Hecho de algodón resistente, ideal para actividades al aire libre o un estilo casual y cómodo.', 50, 85000, 'producto.PNG', 2),

('Pantalón formal gris', 'Pantalón de vestir en color gris, ideal para ocasiones formales. De corte clásico y con un material de alta calidad, perfecto para un look profesional o de oficina.', 35, 115000, 'imagen.webp', 2),

('Pantalón deportivo', 'Pantalón de corte deportivo, ideal para entrenamientos o días casuales. Hecho con material ligero y elástico, asegurando confort y flexibilidad.', 40, 75000, 'producto.PNG', 2),

-- Categoría: Camisetas (category_id = 3)
('Camiseta básica blanca', 'Camiseta básica de algodón en color blanco, perfecta para cualquier ocasión. Con un corte clásico y cómodo, ideal para combinar con jeans o pantalones.', 50, 25000, 'imagen.webp', 3),

('Camiseta estampada', 'Camiseta de algodón con un estampado gráfico moderno, ideal para quienes buscan un estilo más urbano. Disponible en varios colores y tallas.', 40, 29000, 'producto.PNG', 3),

('Camiseta con logo', 'Camiseta básica de color negro con un logo discreto en el pecho. Hecha con algodón suave y cómodo, perfecta para un look casual y relajado.', 60, 28000, 'imagen.webp', 3),

('Camiseta con mensaje', 'Camiseta de algodón con un mensaje gráfico divertido en la parte frontal. Ideal para un look informal y lleno de estilo.', 45, 32000, 'producto.PNG', 3),

('Camiseta de manga larga', 'Camiseta de algodón de manga larga, ideal para los días más frescos. Su diseño sencillo y elegante la hace perfecta para el día a día o para una salida casual.', 30, 35000, 'imagen.webp', 3),

('Camiseta deportiva', 'Camiseta deportiva de material ligero y transpirable, ideal para entrenamientos o actividades físicas. Con un ajuste cómodo y moderno.', 35, 42000, 'producto.PNG', 3),

-- Categoría: Camisas (category_id = 4)
('Camisa de lino blanca', 'Camisa de lino de manga larga, en color blanco. Ideal para el verano, su material fresco y ligero proporciona comodidad durante todo el día. Perfecta para eventos informales o semi-formales.', 30, 99000, 'imagen.webp', 4),

('Camisa de cuadros', 'Camisa de cuadros en tonos rojos y azules. Hecha de algodón suave y cómoda, ideal para un look casual o para una salida informal con amigos.', 50, 85000, 'producto.PNG', 4),

('Camisa de vestir azul', 'Camisa de vestir en color azul, hecha con algodón de alta calidad. Ideal para ocasiones formales o para un look profesional en la oficina.', 40, 120000, 'imagen.webp', 4),

('Camisa de manga corta', 'Camisa de manga corta en color beige, hecha con un tejido ligero y cómodo. Ideal para un día de verano o una salida relajada.', 60, 95000, 'producto.PNG', 4),

('Camisa a rayas', 'Camisa de algodón a rayas en tonos blancos y azules. Perfecta para un look elegante y sofisticado, ideal para reuniones o cenas informales.', 35, 110000, 'imagen.webp', 4),

('Camisa blanca con cuello mao', 'Camisa blanca con cuello mao, ideal para un look más moderno y estilizado. Hecha de algodón de alta calidad, perfecta para ocasiones informales o semi-formales.', 40, 105000, 'producto.PNG', 4),

-- Categoría: Sudaderas (category_id = 5)
('Sudadera gris con capucha', 'Sudadera de algodón con capucha ajustable en color gris. Ideal para los días fríos o para un look cómodo y casual. Disponible en varias tallas.', 50, 89000, 'imagen.webp', 5),

('Sudadera con bolsillo canguro', 'Sudadera con capucha y bolsillo canguro, ideal para un look deportivo o relajado. Hecha con material suave y cómodo, perfecta para cualquier actividad.', 40, 95000, 'producto.PNG', 5),

('Sudadera negra', 'Sudadera de color negro con detalles en contraste. Confeccionada en algodón suave, ideal para un estilo deportivo y cómodo.', 45, 92000, 'imagen.webp', 5),

('Sudadera sin capucha', 'Sudadera de algodón sin capucha, ideal para quienes buscan una opción más ligera. Su diseño simple y elegante la convierte en una prenda versátil para cualquier ocasión casual.', 30, 75000, 'producto.PNG', 5),

('Sudadera con estampado', 'Sudadera de algodón con estampado en la parte frontal, para un look más moderno y único. Ideal para los días fríos o para un estilo casual con un toque urbano.', 35, 99000, 'imagen.webp', 5),

('Sudadera deportiva', 'Sudadera ligera y transpirable, ideal para entrenamientos o actividades deportivas. Con un diseño cómodo y moderno.', 50, 85000, 'producto.PNG', 5),

-- Categoría: Accesorios (category_id = 6)
('Bolso de mano', 'Bolso de mano elegante en color negro, hecho de material de alta calidad. Perfecto para complementar cualquier look. Con espacio adecuado para llevar lo esencial.', 30, 150000, 'imagen.webp', 6),

('Reloj de pulsera', 'Reloj elegante de pulsera, con una correa de cuero y esfera plateada. Ideal para quienes buscan un accesorio sofisticado y funcional.', 40, 220000, 'producto.PNG', 6),

('Gafas de sol', 'Gafas de sol de estilo moderno, con lentes oscuros y marco metálico. Perfectas para proteger tus ojos y agregar estilo a tu outfit.', 50, 80000, 'imagen.webp', 6),

('Cinturón de cuero', 'Cinturón de cuero de alta calidad, en color marrón. Ideal para combinar con jeans o pantalones de vestir, aportando un toque elegante y duradero.', 60, 60000, 'producto.PNG', 6),

('Mochila de tela', 'Mochila casual de tela, perfecta para llevar tus pertenencias de manera cómoda y práctica. Con varios compartimentos para organización.', 40, 70000, 'imagen.webp', 6),

('Sombrero de paja', 'Sombrero de paja estilo fedora, ideal para los días de sol. Perfecto para un look relajado y veraniego, ideal para la playa o una salida casual.', 30, 45000, 'producto.PNG', 6);
