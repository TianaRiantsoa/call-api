-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 17 août 2023 à 11:43
-- Version du serveur :  10.4.19-MariaDB
-- Version de PHP : 7.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `webservices`
--

-- --------------------------------------------------------

--
-- Structure de la table `prestashop`
--

CREATE TABLE `prestashop` (
  `id` int(10) NOT NULL,
  `url` varchar(100) NOT NULL,
  `api` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `prestashop`
--

INSERT INTO `prestashop` (`id`, `url`, `api`) VALUES
(1, 'www.dogexpress.fr', 'ZP7VNYGL8QUX33VSTZ8A7B5ACIJJLHQI'),
(2, 'www.aboralshop.fr', '42YQTXXBCJF6KVC2WARI371MUG2RQXQH'),
(3, 'accessoires.artling.fr', 'NTMKJVO2FH2LDTK05PZNVX4CG2Y0C3QG'),
(4, 'achatnature.com', 'WXBFXNDCUK5728H5XQACDR7NVE9MF6KC'),
(5, 'ajhome.fr', '7LVCHVNY99D7JXAG7FMHJ6XWZGL4I2E8'),
(6, 'b2b.la-loutre.com', '6RJY9JSHSCNZLGUIYZPTG11GHNG8CERP'),
(7, 'baijashop.com', 'S5XV8E54HTXFWDSLI9CJPMYIW9UASZ5J'),
(8, 'www.bain-sanitaire-france.fr', 'SZ5VUFGEZPMWG6EHYREPL6ZHA68IR96N'),
(9, 'www.ocres-de-france.com', 'L8CZEYB9G8BF2AS8EJD60BN2Q2ODCYKR'),
(14, 'www.esthetique-coiffure-ongle.com', 'OCXMZQOVBY0ELTUULBZGBW3P0PZNMNN0'),
(15, 'www.clickforfoot.com', 'TBM8HFFMYLAJSFZEYNC94SX1J6677TVV'),
(16, 'www.thermobaby.com', 'ZB6GPZKJMTQW7QCXH2CHAC46TA3AC1XM'),
(17, 'www.vintagebike.fr', 'YBPNG5T1D4Q7F5TWVIFEH4MLU98GRX79'),
(18, 'www.ammerlaan.fr', 'XPWWERBTZT4ADPY2SVU3VLJNFH7FFDSX'),
(19, 'cf-diffusion.travaux.korigan.fr', 'N9DTJFJH117BCH1F29MFSGGWVRNRPDWQ'),
(20, 'www.espace-ombrage.com', 'HC4NDN5173G2QDVC67F1JZMW3A4WTTMV'),
(21, 'www.jolibump.com', 'VXET7LA17G1CNEZEUZ587PFHRQJHK6FV'),
(22, 'cid.boutique', 'RIIBCYEWVFKW1CFDNXBQ6K23DAJ556QR'),
(23, 'www.espaceclient-gpinternational.fr', 'QCQ4K45ASTA4ZD8ZJZAE99A40N02OFC0'),
(24, 'www.hobbyjoux.com', 'GG2TVTH6E1RNBF4JZ9JV32WEN9CK5X7I'),
(25, 'ddmototeam.15-100-17.com', '2EZN3JDSSKW51ZZUCA9K49SXN6G9U22Z'),
(26, 'www.ahava-france.fr', 'QNPTWNMY99CCD7WYK5WYWGZ3YRQ75YXJ'),
(27, 'www.akashi.fr', '2KUI1FB22B3I6B7TKPN69QY29WD9JAGK'),
(28, 'www.betonavenue.fr', '71CCD5K1VBV2V84WJXLP1HTXH9FMMVBS'),
(29, 'www.autosanit.com', 'MKMBA4B7CN9HJAANGDHWI7RU3SZHPW6K'),
(30, 'www.eliquid-france.com', '776ZX5QKPT33LL7LQSU55PT8MMIDHWPG'),
(31, 'preprod.altumis.com', '1ZRGB8VXNDQ4I8Y9487D12BFT48T6S9P'),
(32, 'preprod.coutumecafe.presta201.axome.cc', 'MBJ772N95QW46M67ZXQ8D835AAKWKST2'),
(33, 'www.dea-aquitaine.fr', '4I7LX7EZ9BAJ7WN86WIYPS1SZTPWS1I9'),
(34, 'www.deasudouest.fr', '4I7LX7EZ9BAJ7WN86WIYPS1SZTPWS1I9'),
(37, '2dfa1110-e597-4172-8323-e8e3af7cc013.parachute.sh', 'EMFPEX2FQ9MAGAS9ERN77S6TB9M13QYE'),
(38, 'boutique.lavacequirit.com', 'TAGS5VHFS559FDKS1NGEEQTU46TXSB1E'),
(39, 'www.grimtout.fr', 'S4JA11AHP69WHH4NFADHJKTRJIW8YAXI'),
(40, 'www.whamisa-france.fr', 'QNPTWNMY99CCD7WYK5WYWGZ3YRQ75YXJ'),
(41, 'www.al-bara.com', 'QNPTWNMY99CCD7WYK5WYWGZ3YRQ75YXJ'),
(42, 'www.egyptianmagic.fr', 'QNPTWNMY99CCD7WYK5WYWGZ3YRQ75YXJ'),
(43, 'www.enviedechamp.com', 'HSDHJKG171SJ5AESVEZTYNHX9TW5VM3X'),
(44, 'www.antares-diffusion.com', 'IWMPG9VX5NIDBX1UTSCW8K172K9MTGRQ'),
(45, 'www.hellin.fr', 'DR6CAZLBK59U7A6Y6PTM9K2KZAOKS4DK'),
(46, 'www.decors-du-monde.com', '4TDD1IZ4K1B8SUA8YQHL84S7BWK4PIEQ'),
(47, 'www.algosud.com', 'WUZ67W2I2HQYDHVMXKEDFZUWSD6VR4Y5'),
(48, 'www.clickoutil.com', '4YWRSFNGQ9CFXAJ7CY2R57CJJ1EXA4C5'),
(49, 'www.foliesdeprovence.com', 'TBF3XS1524ADN8BBE642MDYTNR4QG91R'),
(50, 'www.minimaliste.green', 'QNPTWNMY99CCD7WYK5WYWGZ3YRQ75YXJ'),
(51, 'www.domaspi.com', '5YW7UZ2N5DU41SUHDZK5C5KZNT1QMLDR'),
(52, 'www.cf-diffusion.fr', 'N9DTJFJH117BCH1F29MFSGGWVRNRPDWQ'),
(53, 'www.espaceproanimaux.fr', 'H2HL4ZDURXF2WW92FUMSMJPP27KWMQNB'),
(54, 'bbc.presta218.fr', 'Z2RJH1AWITXWMVXB61AEMHGEMVFA5Z8U'),
(55, 'www.coqenpate.com', 'ZK1S9ZCZDDBSJ2R1MAL6E99EDS8WEFIJ'),
(56, 'dpstudio-fashion.com', 'J1VN8WK9Z5D3AQ122VXFHSSGNTL3YJRR'),
(57, 'thecarameletchocolat.fr', 'VV3V9WDT378Z8LPBVEC1P3BR5F3J48W2'),
(58, 'www.k-eyes.com', 'DKIWKYW3IPZXQL5S4QCFZF1FS3I2TIW7'),
(59, 'www.atf-tech.com', 'PNYH15XNWTIWJ65QEV9S1FRWN6KY4D6N'),
(60, 'seniorama-veti.fr', 'J1ZQWXCTNUQB12U2GBJ3TJHKNHJ3B44K'),
(61, 'www.coroxyl.eu', 'ERQ8R5M3G6NV9AHH48LZ77PDMPL6W53H'),
(62, 'www.solaris-store.com', 'LMZT7Y6SKBQDX41E178QADAI6KKYJU51'),
(63, 'prod.1944.paris', 'G36AE1MFI7EYUDSLX7UMFFI74NYY2AQE'),
(64, 'www.reuniontouspneumatiques.re', 'EKMRWIIE3GBRGA7U9SHWZ373ALZ5N8B8'),
(65, 'b2b.ricochet.fr', 'MMD2NSU3EBALFE7UT1VHJGAR8QC6DUIT'),
(66, 'www.trentotto.fr', 'ZERPIGXBBVSPNLE8Z4AJKNAB9225CM76'),
(67, 'www.universalfood.fr', 'I3MJV2CWQ6UPUKXTPL15R655UWZZMK6Q'),
(68, 'shop.axce-repro.com', 'I4GDU9STJ3MS256E44MA2MF15BGG4VG6'),
(69, 'new.espace-ombrage.com', '6518U7GEDE4P56SQYW1JSDJ6ZW14WTQI'),
(70, 'www.cascadevents.fr', '1AHPCWBGT530N0WWCH6BD20N1VXLM75A'),
(71, 'alleebureau-refonte-pp.latelier42.fr', 'J3V6RK6I75J6M65LDHW5SY93FMMRX61Y'),
(72, 'allee-du-bureau.plif4.demoprestashop.fr', 'J3V6RK6I75J6M65LDHW5SY93FMMRX61Y'),
(73, 'www.agfaphoto-gtc.com', '2PK4I4RBBF62CTS5G982GF6ZBSMSWWFP'),
(74, 'enfildindienne.dev-iidi.com', 'FFFSDG7P4L25DSRWHF2FZW7L34334PIL'),
(75, 'www.famoustshirt.fr', 'TPMQH2DVGY1DXGZ7CBQ4NL98U3QTNYPU'),
(76, 'marketplace.foxchip.com', '08XBBBKTELXQOCC0CD4ZRZOIKXMI06FO'),
(77, 'www.agenc-mag.com', 'W2YKALPDN73K289J5MH5TXCENYA4826K'),
(78, 'inaka.fr', '795GIF3GD6KGWV4WU25M6TQMKZSW8BU6'),
(79, 'alphabiofrance.com', 'CE81BSL7U5AV4RSEBZ5X1W9XRB578EW9'),
(80, 'www.skaii-and-shrimps.fr', 'B4PKEMFV76RQDFEZDCKLDZKU6YIQ8FVJ'),
(81, 'domaspi.preprod-gda.fr', 'L11Q3YR8714ISYERRCG2DLUKLHUDQJEQ'),
(82, 'djoliba.com', 'DM3M5714FJFHWDJCZYWQKDDUJBM78LZR'),
(83, 'enfildindienne.fr', 'FFFSDG7P4L25DSRWHF2FZW7L34334PIL'),
(84, 'linstant-bougies.com', 'E2VT52XR5GI1F2RD8TC82X1V1YQ3Z8QV'),
(85, 'jumbobag.fr', 'P77YU3ZB8SV1EB82F4THCVDX1JT5A8HM'),
(86, 'innov-pratic.com', 'HX3ETGXJ2ZU3MYX3H9XCZWP2BCX8JTA5'),
(87, 'www.imiza.com', 'S7SYZFLVB63V5VMQ516X2B9LC6GH8SNK'),
(88, 'www.espace-orthophonie.fr', 'WMYNTET1MVL7Z3DN9LQMLSP5ASMZSF2J'),
(89, 'innov-pratic.com', 'T7KBEIX89IJBZBTRPQE22542CNHUFSMN'),
(90, 'www.pcp.tv', 'UUX2LLDAAZIXRFK19KDIKE1YQ9G3T91E'),
(91, 'www.best-of-land.com', 'MA2TW42WHTQ4R4HVB6HHRL9KPRX95EZE'),
(92, 'dev.aries-esthetique.com', 'YAHPQTYYCXBKCZJGIH2AS1GJD1INCI2N'),
(93, 'boutique.stademontoisrugby.fr', 'B4UG4MFB1S52P6C7BRAJD8XV5Y3VMHX3'),
(94, 'preprodcif.fr', 'I7UBXAE65BVN1RZ6JFAEIQDG3STK5RGL'),
(95, 'preprodcif.fr/retail', 'I7UBXAE65BVN1RZ6JFAEIQDG3STK5RGL'),
(96, 'www.optima-distribution.fr', 'PSQKW6FTYWIVCMREE4JP8RRMCMHEXL2F'),
(97, 'www.monting.fr', 'N012DIJ13GB3NVEHBUA7NWH10G545V0Z'),
(98, 'localhost:8080/prestashop_1.7.7.8', 'E94CQP4MHV5LKZLNR6JLYT29AAS9QATY '),
(99, 'www.sarlatoutillage.fr', '1FAHAMVSJLMWIBZUUXB24WNZKGWWAGRA'),
(100, 'www.astuning.fr', 'HX7UDGLJ4XDJYNJQPAGWTVEVNVE1NM5E');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `prestashop`
--
ALTER TABLE `prestashop`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `prestashop`
--
ALTER TABLE `prestashop`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;