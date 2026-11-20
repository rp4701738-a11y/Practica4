-- =========================================================================
-- 1. CREACIÓN DE LA BASE DE DATOS
-- =========================================================================
DROP DATABASE IF EXISTS pibd;
CREATE DATABASE pibd
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE pibd;

-- =========================================================================
-- 2. TABLAS MAESTRAS (con datos modelo, como tipo de anuncio, países...)
-- =========================================================================

CREATE TABLE Paises (
    IdPais INT AUTO_INCREMENT PRIMARY KEY,
    NomPais VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Estilos (
    IdEstilo INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Descripcion TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE TiposAnuncios (
    IdTAnuncio SMALLINT AUTO_INCREMENT PRIMARY KEY,
    NomTAnuncio VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE TiposViviendas (
    IdTVivienda SMALLINT AUTO_INCREMENT PRIMARY KEY,
    NomTVivienda VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE TiposMensajes (
    IdTMensaje SMALLINT AUTO_INCREMENT PRIMARY KEY,
    NomTMensaje VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- 3. TABLAS PRINCIPALES
-- =========================================================================

CREATE TABLE Usuarios (
    IdUsuario INT AUTO_INCREMENT PRIMARY KEY,
    NomUsuario VARCHAR(15) NOT NULL UNIQUE,
    Clave VARCHAR(255) NOT NULL,
    Email VARCHAR(254) NOT NULL,
    Sexo SMALLINT,
    FNacimiento DATE,
    Ciudad VARCHAR(150),
    Pais INT,
    Foto VARCHAR(255),
    FRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Estilo INT,
    CONSTRAINT fk_usuarios_pais
        FOREIGN KEY (Pais) REFERENCES Paises(IdPais)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_usuarios_estilo
        FOREIGN KEY (Estilo) REFERENCES Estilos(IdEstilo)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Anuncios (
    IdAnuncio INT AUTO_INCREMENT PRIMARY KEY,
    TAnuncio SMALLINT NOT NULL,
    TVivienda SMALLINT NOT NULL,
    FPrincipal VARCHAR(255),
    Alternativo VARCHAR(255) NOT NULL,
    Titulo VARCHAR(255) NOT NULL,
    Precio DECIMAL(12, 2),
    Texto TEXT,
    Ciudad VARCHAR(150),
    Pais INT,
    Superficie DECIMAL(10, 2),
    NHabitaciones INT,
    NBanyos INT,
    Planta INT,
    Anyo INT,
    FRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Usuario INT NOT NULL,
    CONSTRAINT chk_anuncios_alternativo CHECK (CHAR_LENGTH(Alternativo) >= 10),
    CONSTRAINT fk_anuncios_tanuncio
        FOREIGN KEY (TAnuncio) REFERENCES TiposAnuncios(IdTAnuncio)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_anuncios_tvivienda
        FOREIGN KEY (TVivienda) REFERENCES TiposViviendas(IdTVivienda)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_anuncios_pais
        FOREIGN KEY (Pais) REFERENCES Paises(IdPais)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_anuncios_usuario
        FOREIGN KEY (Usuario) REFERENCES Usuarios(IdUsuario)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Fotos (
    IdFoto INT AUTO_INCREMENT PRIMARY KEY,
    Titulo VARCHAR(255),
    Foto VARCHAR(255) NOT NULL,
    Alternativo VARCHAR(255) NOT NULL,
    Anuncio INT NOT NULL,
    CONSTRAINT chk_fotos_alternativo CHECK (CHAR_LENGTH(Alternativo) >= 10),
    CONSTRAINT fk_fotos_anuncio
        FOREIGN KEY (Anuncio) REFERENCES Anuncios(IdAnuncio)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Solicitudes (
    IdSolicitud INT AUTO_INCREMENT PRIMARY KEY,
    Anuncio INT NOT NULL,
    Texto VARCHAR(4000),
    Nombre VARCHAR(200) NOT NULL,
    Email VARCHAR(254) NOT NULL,
    Direccion TEXT,
    Telefono VARCHAR(20),
    Color VARCHAR(50),
    Copias INT,
    Resolucion INT,
    Fecha DATE,
    IColor BOOLEAN,
    IPrecio BOOLEAN,
    FRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Coste DECIMAL(10, 2),
    CONSTRAINT fk_solicitudes_anuncio
        FOREIGN KEY (Anuncio) REFERENCES Anuncios(IdAnuncio)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Mensajes (
    IdMensaje INT AUTO_INCREMENT PRIMARY KEY,
    TMensaje SMALLINT NOT NULL,
    Texto VARCHAR(4000) NOT NULL,
    Anuncio INT NOT NULL,
    UsuOrigen INT NOT NULL,
    UsuDestino INT NOT NULL,
    FRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mensajes_tmensaje
        FOREIGN KEY (TMensaje) REFERENCES TiposMensajes(IdTMensaje)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_mensajes_anuncio
        FOREIGN KEY (Anuncio) REFERENCES Anuncios(IdAnuncio)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_mensajes_origen
        FOREIGN KEY (UsuOrigen) REFERENCES Usuarios(IdUsuario)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_mensajes_destino
        FOREIGN KEY (UsuDestino) REFERENCES Usuarios(IdUsuario)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- 4. INSERCIÓN DE DATOS EN TABLAS MAESTRAS
-- =========================================================================

INSERT INTO Paises (NomPais) VALUES
('España'),
('Francia'),
('Portugal'),
('Italia'),
('Alemania');

INSERT INTO Estilos (Nombre, Descripcion) VALUES
('Sin', 'Estilo por defecto sin personalización'),
('ModOsc', 'Estilo adaptado con fondo oscuro y colores claros'),
('LetrGran', 'Estilo con letra grande para mejor legibilidad'),
('AltContr', 'Estilo con alto contraste para accesibilidad visual'),
('ModOsc+LetrGran', 'Combinación del modo oscuro con letra grande'),
('ModOsc+AltContr', 'Combinación del modo oscuro con alto contraste'),
('LetrGran+AltContr', 'Combinación de letra grande con alto contraste'),
('ModOsc+LetrGran+AltContr', 'Combinación del modo oscuro, letra grande y alto contraste');

INSERT INTO TiposAnuncios (NomTAnuncio) VALUES
('Venta'),
('Alquiler');

INSERT INTO TiposViviendas (NomTVivienda) VALUES
('Obra nueva'),
('Vivienda'),
('Oficina'),
('Local'),
('Garaje');

INSERT INTO TiposMensajes (NomTMensaje) VALUES
('Más información'),
('Solicitar una cita'),
('Comunicar una oferta');

-- =========================================================================
-- 5. INSERCIÓN DE DATOS DE PRUEBA
-- =========================================================================

INSERT INTO Usuarios (NomUsuario, Clave, Email, Sexo, FNacimiento, Ciudad, Pais, Foto, FRegistro, Estilo) VALUES
('usuario1', '1234', 'usuario1@buscasa.es', 0, '1996-03-15', 'Alicante', 1, 'imagenes/casa1.jpeg', '2026-10-01 10:15:00', 2),
('usuario2', '5678', 'usuario2@buscasa.es', 1, '1992-08-22', 'Valencia', 1, 'imagenes/apartamento1.jpeg', '2026-10-08 16:30:00', 3),
('usuario3', 'abcd', 'usuario3@buscasa.es', 0, '1989-11-04', 'París', 2, 'imagenes/casa1.jpeg', '2026-10-15 09:45:00', 4),
('usuario4', 'efgh', 'usuario4@buscasa.es', 1, '1999-05-19', 'Lisboa', 3, 'imagenes/apartamento1.jpeg', '2026-10-22 18:20:00', 7);

INSERT INTO Anuncios (TAnuncio, TVivienda, FPrincipal, Alternativo, Titulo, Precio, Texto, Ciudad, Pais, Superficie, NHabitaciones, NBanyos, Planta, Anyo, FRegistro, Usuario) VALUES
(2, 2, 'imagenes/apartamento1.jpeg', 'Salón luminoso de piso céntrico en Alicante', 'Piso luminoso en el centro de Alicante', 850.00, 'Vivienda totalmente amueblada a cinco minutos de la playa y del tranvía.', 'Alicante', 1, 85.50, 3, 2, 4, 2010, '2026-10-25 11:00:00', 1),
(1, 1, 'imagenes/casa1.jpeg', 'Fachada moderna de obra nueva en Valencia', 'Promoción obra nueva junto a Ruzafa', 265000.00, 'Viviendas de diseño con calificación energética A y piscina comunitaria.', 'Valencia', 1, 105.00, 3, 2, 2, 2026, '2026-10-28 12:30:00', 2),
(2, 3, 'imagenes/apartamento1.jpeg', 'Oficina diáfana con ventanales en Madrid', 'Oficina moderna en zona financiera', 1400.00, 'Espacio de trabajo diáfano con fibra óptica, climatización y sala de juntas.', 'Madrid', 1, 120.00, 2, 1, 6, 2018, '2026-10-30 09:45:00', 1),
(2, 4, 'imagenes/casa1.jpeg', 'Local comercial a pie de calle en Alicante', 'Local comercial en avenida principal', 1100.00, 'Amplio escaparate, salida de humos y almacén trasero en zona muy transitada.', 'Alicante', 1, 95.00, 1, 1, 0, 2005, '2026-11-02 17:20:00', 2),
(1, 5, 'imagenes/apartamento1.jpeg', 'Plaza de garaje amplia en el centro de Sevilla', 'Plaza de garaje para coche grande', 19500.00, 'Fácil maniobra, puerta automática y vigilancia 24 horas.', 'Sevilla', 1, 16.50, 0, 0, -1, 2015, '2026-11-05 14:10:00', 3),
(2, 2, 'imagenes/apartamento1.jpeg', 'Apartamento con vistas al Sena en París', 'Apartamento reformado en Le Marais', 1850.00, 'Encantador apartamento con suelos de parqué y balcón exterior.', 'París', 2, 62.00, 2, 1, 3, 1985, '2026-11-08 18:00:00', 3),
(1, 2, 'imagenes/casa1.jpeg', 'Chalet independiente con jardín en Lisboa', 'Casa familiar con patio en Lisboa', 420000.00, 'Casa espaciosa en barrio tranquilo con jardín privado y garaje.', 'Lisboa', 3, 180.00, 4, 3, 0, 2012, '2026-11-11 10:05:00', 4),
(2, 2, 'imagenes/apartamento1.jpeg', 'Estudio reformado cerca del Coliseo en Roma', 'Estudio acogedor en el centro de Roma', 1200.00, 'Ideal para estancias largas, cocina americana y aire acondicionado.', 'Roma', 4, 48.00, 1, 1, 2, 1995, '2026-11-14 13:40:00', 4),
(1, 2, 'imagenes/casa1.jpeg', 'Ático con terraza amplia y vistas en Alicante', 'Ático dúplex con vistas al mar', 310000.00, 'Terraza de 40 metros cuadrados, orientación sur y plaza de garaje incluida.', 'Alicante', 1, 115.00, 3, 2, 7, 2021, '2026-11-16 19:15:00', 1),
(2, 5, 'imagenes/apartamento1.jpeg', 'Plaza de garaje cubierta en Berlín centro', 'Garaje vigilado en Mitte', 180.00, 'Plaza subterránea con acceso 24 horas y cargador eléctrico.', 'Berlín', 5, 15.00, 0, 0, -2, 2020, '2026-11-18 11:30:00', 2);

INSERT INTO Fotos (Titulo, Foto, Alternativo, Anuncio) VALUES
('Cocina equipada', 'imagenes/apartamento1.jpeg', 'Cocina equipada del piso céntrico en Alicante', 1),
('Dormitorio principal', 'imagenes/casa1.jpeg', 'Dormitorio principal luminoso en Alicante', 1),
('Terraza comunitaria', 'imagenes/casa1.jpeg', 'Terraza comunitaria de obra nueva en Valencia', 2),
('Sala de reuniones', 'imagenes/apartamento1.jpeg', 'Sala de reuniones de la oficina en Madrid', 3),
('Escaparate exterior', 'imagenes/casa1.jpeg', 'Escaparate amplio del local comercial en Alicante', 4),
('Balcón exterior', 'imagenes/apartamento1.jpeg', 'Balcón exterior del apartamento en París', 6),
('Jardín trasero', 'imagenes/casa1.jpeg', 'Jardín privado trasero de la casa en Lisboa', 7),
('Vista nocturna de la terraza', 'imagenes/casa1.jpeg', 'Terraza amplia con vistas al mar en Alicante', 9);

INSERT INTO Mensajes (TMensaje, Texto, Anuncio, UsuOrigen, UsuDestino, FRegistro) VALUES
(1, 'Hola, ¿los gastos de comunidad están incluidos en el precio del alquiler?', 1, 2, 1, '2026-10-26 10:00:00'),
(2, 'Me gustaría concertar una visita para ver la promoción de Ruzafa esta semana.', 2, 1, 2, '2026-10-29 15:20:00'),
(3, 'Estoy interesado en el ático, ¿aceptarías una oferta de 295.000 euros?', 9, 3, 1, '2026-11-17 11:30:00'),
(1, '¿El apartamento de París admite mascotas pequeñas?', 6, 1, 3, '2026-11-09 18:45:00'),
(2, '¿Podríamos visitar la casa de Lisboa el próximo viernes por la tarde?', 7, 2, 4, '2026-11-12 09:15:00'),
(1, '¿El estudio en Roma tiene ascensor y calefacción individual?', 8, 3, 4, '2026-11-15 16:05:00'),
(3, 'Te propongo un alquiler anual con pago semestral por el local de Alicante.', 4, 4, 2, '2026-11-16 12:10:00'),
(2, '¿Es posible probar si cabe un SUV grande en la plaza de Sevilla?', 5, 4, 3, '2026-11-18 17:50:00');

INSERT INTO Solicitudes (Anuncio, Texto, Nombre, Email, Direccion, Telefono, Color, Copias, Resolucion, Fecha, IColor, IPrecio, FRegistro, Coste) VALUES
(1, 'Folleto para repartir en la zona centro', 'Usuario Uno', 'usuario1@buscasa.es', 'Av. Alfonso el Sabio 12, 3ºB, 03004 Alicante, España', '600123456', '#1E3A8A', 20, 300, '2026-11-25', TRUE, TRUE, '2026-11-10 12:00:00', 18.50),
(2, 'Impresión en alta resolución para feria inmobiliaria', 'Usuario Dos', 'usuario2@buscasa.es', 'C/ Colón 45, 1ºA, 46004 Valencia, España', '611987654', '#000000', 50, 600, '2026-12-01', FALSE, TRUE, '2026-11-12 17:10:00', 34.00);

-- =========================================================================
-- 6. CREACIÓN DEL USUARIO PARA LA CONEXIÓN WEB (PERMISOS MÍNIMOS)
-- =========================================================================
CREATE USER IF NOT EXISTS 'wwwdata'@'localhost' IDENTIFIED BY 'abc';
GRANT SELECT, INSERT, UPDATE, DELETE ON pibd.* TO 'wwwdata'@'localhost';
FLUSH PRIVILEGES;