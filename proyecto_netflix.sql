--Base de datos: proyecto_netflix  (versión corregida 1.3)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS `proyecto_netflix`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `proyecto_netflix`;

DROP TABLE IF EXISTS `Cuentas`;
CREATE TABLE `Cuentas` (
  `id`        INT(11)      NOT NULL AUTO_INCREMENT,
  `nombre`    VARCHAR(60)  NOT NULL,
  `apellido`  VARCHAR(60)  NOT NULL,
  `usuario`   VARCHAR(30)  NOT NULL,
  `edad`      INT(3)       NOT NULL,
  `email`     VARCHAR(100) NOT NULL,
  `password`  VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `email`   (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `peliculas`;
CREATE TABLE `peliculas` (
  `id`          INT(11)      NOT NULL AUTO_INCREMENT,
  `titulo`      VARCHAR(150) NOT NULL,
  `descripcion` TEXT         NOT NULL,
  `genero`      VARCHAR(50)  NOT NULL,
  `anio`        YEAR         NOT NULL,
  `portada`     VARCHAR(300) NOT NULL DEFAULT 'img/placeholder.png',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--Inserciones basicas para que no este vacio el catalogo
INSERT INTO `peliculas` (`titulo`, `descripcion`, `genero`, `anio`, `portada`) VALUES
('Stranger Things',      'Un grupo de niños descubre misterios sobrenaturales en su pueblo.',          'Ciencia ficción', 2016, 'https://image.tmdb.org/t/p/w500/49WJfeN0moxb9IPfGn8AIqMGskD.jpg'),
('Breaking Bad',         'Un profesor de química se convierte en fabricante de metanfetamina.',         'Drama',           2008, 'https://image.tmdb.org/t/p/w500/ggFHVNu6YYI5L9pCfOacjizRGt.jpg'),
('The Witcher',          'Un cazador de monstruos lucha por encontrar su lugar en un mundo peligroso.', 'Fantasía',        2019, 'https://image.tmdb.org/t/p/w500/7vjaCdMw15FEbXyLQTVa04URsPm.jpg'),
('Dark',                 'Cuatro familias alemanas buscan a sus hijos desaparecidos en un pueblo.',     'Misterio',        2017, 'https://image.tmdb.org/t/p/w500/apbrbWs8M9lyOpJYU5WXrpFbk1Z.jpg'),
('Ozark',                'Un asesor financiero lava dinero para un cártel en los Lagos Ozark.',         'Thriller',        2017, 'https://image.tmdb.org/t/p/w500/pCGyPEMaOGHB7F8vd7pQnBXvLJL.jpg'),
('Squid Game',           'Personas con deudas compiten en juegos mortales por un gran premio.',         'Acción',          2021, 'https://image.tmdb.org/t/p/w500/dDlEmu3EZ0XkD1m8WLkIRQWzHjb.jpg'),
('Wednesday',            'Wednesday Addams investiga crímenes sobrenaturales en su nueva escuela.',     'Comedia oscura',  2022, 'https://image.tmdb.org/t/p/w500/9PFonBhy4cQy7Jz20NpMygczOkv.jpg'),
('The Crown',            'La historia de la Familia Real Británica a lo largo de las décadas.',         'Drama histórico', 2016, 'https://image.tmdb.org/t/p/w500/1M876KPjulVwppEpldhdc8V4o68.jpg'),
('Money Heist',          'Un genio criminal planea el robo más ambicioso de la historia de España.',    'Thriller',        2017, 'https://image.tmdb.org/t/p/w500/reEMJA1uzscCbkpeRJeTT2bjqUp.jpg'),
('Black Mirror',         'Antología que explora el lado oscuro de la tecnología y la sociedad.',        'Distopía',        2011, 'https://image.tmdb.org/t/p/w500/7PRddO7z7mcPi21nZTCMGShAyy1.jpg');

COMMIT;
