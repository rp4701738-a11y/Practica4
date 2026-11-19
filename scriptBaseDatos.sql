-- =========================================================================
-- 1. CREACIÓN DE LA BASE DE DATOS
-- =========================================================================
DROP DATABASE IF EXISTS pibd;
CREATE DATABASE pibd
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE pibd;

-- =========================================================================
-- 2. TABLAS MAESTRAS (Con datos fijos, como tipo de mensaje o países)
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

-- Usuarios (Nota: las claves están en texto plano para pruebas iniciales;
-- sustituir por hashes si se implementa password_hash() en PHP)
INSERT INTO Usuarios (NomUsuario, Clave, Email, Sexo, FNacimiento, Ciudad, Pais, Foto, FRegistro, Estilo) VALUES
('ana_garcia', 'clave1234', 'ana.garcia@example.com', 1, '1995-04-12', 'Alicante', 1, 'imagenes/ana.jpg', '2026-09-10 10:15:00', 1),
('carlos_m', 'clave1234', 'carlos.m@example.com', 0, '1990-11-23', 'Valencia', 1, 'imgagenes/carlos.jpg', '2026-09-15 16:30:00', 2),
('laura_p', 'clave1234', 'laura.p@example.com', 1, '1998-07-05', 'París', 2, 'imgagenes/laura.jpg', '2026-10-01 09:00:00', 1);

-- Anuncios (9 anuncios variados en tipo, vivienda, ciudad, país y fecha de registro)
INSERT INTO Anuncios (TAnuncio, TVivienda, FPrincipal, Alternativo, Titulo, Precio, Texto, Ciudad, Pais, Superficie, NHabitaciones, NBanyos, Planta, Anyo, FRegistro, Usuario) VALUES
(2, 2, 'imagenes/anuncio1.jpg', 'Salón luminoso de piso céntrico en Alicante', 'Piso luminoso en el centro de Alicante', 850.00, 'Vivienda totalmente amueblada a cinco minutos de la playa y del tranvía.', 'Alicante', 1, 85.50, 3, 2, 4, 2010, '2026-10-05 11:00:00', 1),
(1, 1, 'imagenes/anuncio2.jpg', 'Fachada moderna de obra nueva en Valencia', 'Promoción obra nueva junto a Ruzafa', 265000.00, 'Viviendas de diseño con calificación energética A y piscina comunitaria.', 'Valencia', 1, 105.00, 3, 2, 2, 2026, '2026-10-10 12:30:00', 2),
(2, 3, 'imagenes/anuncio3.jpg', 'Oficina diáfana con ventanales en Madrid', 'Oficina moderna en zona financiera', 1400.00, 'Espacio de trabajo diáfano con fibra óptica, climatización y sala de juntas.', 'Madrid', 1, 120.00, 2, 1, 6, 2018, '2026-10-14 09:45:00', 1),
(2, 4, 'imagenes/anuncio4.jpg', 'Local comercial a pie de calle en Alicante', 'Local comercial en avenida principal', 1100.00, 'Amplio escaparate, salida de humos y almacén trasero en zona muy transitada.', 'Alicante', 1, 95.00, 1, 1, 0, 2005, '2026-10-18 17:20:00', 2),
(1, 5, 'imagenes/anuncio5.jpg', 'Plaza de garaje amplia en el centro de Sevilla', 'Plaza de garaje para coche grande', 19500.00, 'Fácil maniobra, puerta automática y vigilancia 24 horas.', 'Sevilla', 1, 16.50, 0, 0, -1, 2015, '2026-10-22 14:10:00', 3),
(2, 2, 'imagenes/anuncio6.jpg', 'Apartamento con vistas al Sena en París', 'Apartamento reformado en Le Marais', 1850.00, 'Encantador apartamento con suelos de parqué y balcón exterior.', 'París', 2, 62.00, 2, 1, 3, 1985, '2026-10-27 18:00:00', 3),
(1, 2, 'imagenes/anuncio7.jpg', 'Chalet independiente con jardín en Lisboa', 'Casa familiar con patio en Lisboa', 420000.00, 'Casa espaciosa en barrio tranquilo con jardín privado y garaje.', 'Lisboa', 3, 180.00, 4, 3, 0, 2012, '2026-11-02 10:05:00', 2),
(2, 2, 'imagenes/anuncio8.jpg', 'Estudio reformado cerca del Coliseo en Roma', 'Estudio acogedor en el centro de Roma', 1200.00, 'Ideal para estancias largas, cocina americana y aire acondicionado.', 'Roma', 4, 48.00, 1, 1, 2, 1995, '2026-11-08 13:40:00', 3),
(1, 2, 'imagenes/anuncio9.jpg', 'Ático con terraza amplia en Alicante', 'Ático dúplex con vistas al mar', 310000.00, 'Terraza de 40 metros cuadrados, orientación sur y plaza de garaje incluida.', 'Alicante', 1, 115.00, 3, 2, 7, 2021, '2026-11-15 19:15:00', 1);

-- Fotos adicionales asociadas a anuncios
INSERT INTO Fotos (Titulo, Foto, Alternativo, Anuncio) VALUES
('Cocina equipada', 'imagenes/anuncio1_cocina.jpg', 'Cocina equipada del piso céntrico en Alicante', 1),
('Dormitorio principal', 'imagenes/anuncio1_hab.jpg', 'Dormitorio principal luminoso en Alicante', 1),
('Terraza comunitaria', 'imagenes/anuncio2_terraza.jpg', 'Terraza comunitaria de obra nueva en Valencia', 2),
('Sala de reuniones', 'imagenes/anuncio3_sala.jpg', 'Sala de reuniones de la oficina en Madrid', 3),
('Vista nocturna de la terraza', 'imagenes/anuncio9_terraza.jpg', 'Terraza amplia con vistas al mar en Alicante', 9);

-- Mensajes cruzados entre usuarios
INSERT INTO Mensajes (TMensaje, Texto, Anuncio, UsuOrigen, UsuDestino, FRegistro) VALUES
(1, 'Hola Ana, ¿los gastos de comunidad están incluidos en el alquiler?', 1, 2, 1, '2026-10-06 10:00:00'),
(2, 'Me gustaría concertar una visita para ver la promoción de Ruzafa esta semana.', 2, 1, 2, '2026-10-12 15:20:00'),
(3, 'Estoy interesada en el ático, ¿aceptarías una oferta de 295.000 euros?', 9, 3, 1, '2026-11-16 11:30:00'),
(1, '¿El apartamento de París admite mascotas?', 6, 1, 3, '2026-11-01 18:45:00');

-- Solicitudes de folleto de prueba
INSERT INTO Solicitudes (Anuncio, Texto, Nombre, Email, Direccion, Telefono, Color, Copias, Resolucion, Fecha, IColor, IPrecio, FRegistro, Coste) VALUES
(1, 'Folleto para repartir en la zona centro', 'Ana García López', 'ana.garcia@example.com', 'Av. Alfonso el Sabio 12, 3ºB, 03004 Alicante, España', '600123456', '#1E3A8A', 20, 300, '2026-11-25', TRUE, TRUE, '2026-11-10 12:00:00', 18.50),
(2, 'Impresión en alta resolución para feria inmobiliaria', 'Carlos Martínez Ruiz', 'carlos.m@example.com', 'C/ Colón 45, 1ºA, 46004 Valencia, España', '611987654', '#000000', 50, 600, '2026-12-01', FALSE, TRUE, '2026-11-12 17:10:00', 34.00);

-- =========================================================================
-- 6. CREACIÓN DEL USUARIO PARA LA CONEXIÓN WEB (PERMISOS MÍNIMOS)
-- =========================================================================
CREATE USER IF NOT EXISTS 'wwwdata'@'localhost' IDENTIFIED BY 'abc';
GRANT SELECT, INSERT, UPDATE, DELETE ON pibd.* TO 'wwwdata'@'localhost';
FLUSH PRIVILEGES;