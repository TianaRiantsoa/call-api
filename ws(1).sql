-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mer. 24 juil. 2024 à 10:58
-- Version du serveur : 8.0.30
-- Version de PHP : 8.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ws`
--

-- --------------------------------------------------------

--
-- Structure de la table `prestashop`
--

CREATE TABLE `prestashop` (
  `id` int NOT NULL,
  `url` varchar(255) NOT NULL,
  `api_key` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `prestashop`
--

INSERT INTO `prestashop` (`id`, `url`, `api_key`) VALUES
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
(20, 'www.espace-ombrage.com', '6518U7GEDE4P56SQYW1JSDJ6ZW14WTQI'),
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
(100, 'www.astuning.fr', 'HX7UDGLJ4XDJYNJQPAGWTVEVNVE1NM5E'),
(103, 'bes-menuiserie.fr', 'WBFYGLV3DVV5KJXHEDJGVV7IELTSPHTJ'),
(104, 'bois-chauffage-dunkerque.fr', 'WBFYGLV3DVV5KJXHEDJGVV7IELTSPHTJ'),
(105, 'www.be-wak.fr', 'GA6UN7J4TTL6FRHMNKPM1S1IK41LN2PG'),
(106, 'cif.fr', '1MFE8KRGM3NZS979XG5QSKNV9J1MTJUQ'),
(107, 'cif.fr/prod', 'QA9KAZS6J286KBYQ9CV96FWQBJHFE3FD'),
(108, 'ci2.vaiso.net', 'WA41QVAQWDZKSFYMBB9P774W9839CDXH'),
(109, 'www.atelier-piscine.com', 'GEE83MIFTBQA4L8PCLRMSWNXFX4QUWTI'),
(110, 'cledical.quatrys.fr', '9FY6ILG2C8L4I7KLYBMYIAJM7V6EIYCT'),
(111, 'cledical.quatrys.fr', '9FY6ILG2C8L4I7KLYBMYIAJM7V6EIYCT'),
(112, 'www.pa-design.com', '2P17D734W9H2ASZLGSSYNCYAKSB87NFZ'),
(113, 'cryokin.fr/eshop', '6RXYYA7HXQHU8CYZWX3P9BCD5I1DUWFU'),
(114, 'www.boutique-outilor.com', 'EHCXZDP4HLCNBY3ZYSEUE89K4UWVJA1G'),
(115, 'www.auxabeillesalpines.fr', 'X1J38IMJFY5Y6CQ5R3EZ5KUDEYHHFKK1'),
(116, 'www.k-eyespro.com', 'DKIWKYW3IPZXQL5S4QCFZF1FS3I2TIW7'),
(117, 'www.tyr.eu', 'FBQPNH4ZBBY35B9ZASJ7C3PW8W4H4QY8'),
(119, 'boutique.stademontoisrugby.fr', 'B4UG4MFB1S52P6C7BRAJD8XV5Y3VMHX3');

-- --------------------------------------------------------

--
-- Structure de la table `shopify`
--

CREATE TABLE `shopify` (
  `id` int NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `shopify`
--

INSERT INTO `shopify` (`id`, `url`, `api_key`, `password`, `secret_key`) VALUES
(2, 'wasteless-eco-packaging.myshopify.com', '3c0c677801245737028eca94af6a3eab', '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', ''),
(3, 'bagages-24h-le-mans.myshopify.com', '6601c43c5db2c6a52ac3994a88e72344', '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', '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'),
(4, 'accessoires-artling.myshopify.com', '1443ff9a24e0ab4f754526d052497aae', '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', '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'),
(5, 'by-ekobo-com.myshopify.com', '775fb9714cfcce4b27232947177274a7', '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', ''),
(6, 'misterbean.myshopify.com', 'bdf3ca5042f3d72086d2776256fe4980', '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', ''),
(7, 'fernando-pensato-gourmet.myshopify.com', 'f1b4a422a0753fca9c7ac8c7a2577c18', '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', ''),
(8, 'inoui-editions-eu.myshopify.com', '15cdb838fae1a3f71151d00b23659c65', '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', ''),
(9, 'allocarrelage1.myshopify.com', '812af06671108f53887978c35ece78e3', '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', ''),
(10, 'nutrivie.myshopify.com', '0cec4fc5fbcb18e71667779b5c1c843b', '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', '8772c9323ea53459754fc256fa82eafa'),
(11, 'rentside.myshopify.com', 'cc0d65dcf94fbd49b55dc69799b99e68', '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', ''),
(12, 'inoui-editions-staging.myshopify.com', '79c1600a8ac00b5686f8c9a8ba098161', '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', 'd75002a7ede72ba6391a0dae2768cfa9'),
(15, 'ec372c.myshopify.com', 'd02119ac444badae4f6fc14ffe941a46', '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', ''),
(16, 'latitude-nature-boutique-dev-connecteur.myshopify.com', '66e901f0110f5ae20a2f1a196ee082be', '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', ''),
(17, 'latitude-preprod.myshopify.com', 'f722f7eba076b1066a7354886e334a08', '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', '1cfdc88723e9c658d746c3080ddb81e9'),
(18, 'vaisonet-test.myshopify.com', 'd7c2343f4b2f24eba6dc9c8195460f1b', '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', '9b2bbd0cf5cb126ec7a147cdcf123cc6'),
(19, 'green-garantie.myshopify.com', 'ad89cb8b116cd099137e53e9a520cc01', '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', '961c85a633dcde6445b4401d228c35fc'),
(20, 'sr-suntour-fr.myshopify.com', 'f1d74a1703e83a50c1b58a73c8ebfd35', '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', 'ffea62419aa0c035f7c830fbedd96696');

-- --------------------------------------------------------

--
-- Structure de la table `woocommerce`
--

CREATE TABLE `woocommerce` (
  `id` int NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `consumer_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `consumer_secret` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `woocommerce`
--

INSERT INTO `woocommerce` (`id`, `url`, `consumer_key`, `consumer_secret`) VALUES
(1, 'tournier-machines-bois.com', 'ck_6e9e5b304b8beef5dcbc7ebd095a6cf23085c164', 'cs_91d35cee5cfaa08d34021f8c2d341164881c0a67'),
(2, 'cafes-goneo.fr', 'ck_15bbeeaa5b581ce66dbf2ef4a48e067981c2c86f', 'cs_12377043bf91066f60ee957e08e291ecbb4ba58a'),
(3, 'www.neoludis.com', 'ck_15caa0014a8284b724bfcc01fa064a39837f9cd8', 'cs_46188b8fe481a9df90d2aa2dffe291eeec35d150'),
(4, 'linwin.bartmedia.eu', 'ck_ec75a1dcef54556758e2a1ea78ab67de685e40e7', 'cs_7d37787605173b4c347296f29cf2178ce3c9ce73'),
(5, 'www.couleurs-du-sud.fr', 'ck_0d4bef3748417a399932f2606e678f02b7d29657', 'cs_e231fe2f3528aac9bf4c456cd384831f98879141'),
(6, 'www.leonorgreyl.com', 'ck_6d84b83501a632bfbd1e39f35d030782915e4421', 'cs_64425b4b3f295cb7b54b0952b262f0d7bd2e6e18'),
(7, 'www.simurgheducation.com', 'ck_3a57805558381a9ba3ec8eaf816150efe89cec1b', 'cs_16e4c0bc433a2424eae5fcfddd7de453c8e89636'),
(8, 'www.volt-corp.com', 'ck_874f8218ebd0e50d41f3a55f8d612c42e737051a', 'cs_2bc512db66abf13676538f6e3325876c8685dcb8'),
(9, 'production-webrunner.tech', 'ck_f3a4bfe2a93ddd102e016cdf9d9b62d977da9105', 'cs_bcfe28f6b201bda7fc5006dc50fbe51363569078'),
(10, 'lamaisondejudith.ovh ', 'ck_1a4c3f8548f2770d52e77593bc58b55d60d1a8ae', 'cs_bbb8e363e12ab99789cbfeda44924a2a9de6d9ff'),
(11, 'boutique-afsr.com', 'ck_a451d49cc9cc651d68872ae48d9d7dbd14b54e1f', 'cs_e8eaf01163c5d92c4b2594bc348ba701a633535a'),
(12, 'cafe-proqua.com', 'ck_f5ce969e3d83808a28b1173fe031002bd673912d', 'cs_567268deda759fe50ce8a6184bcec2ae273f1d36'),
(13, 'www.nouvel-oeil.net/cobalt', 'ck_f7f7e2124cbfb3cebf5c43840ce66f831d7003be', 'cs_d2786c6770f8242455b2c3862e6cf2ffa9b5c363'),
(14, 'www.vwsports.fr', 'ck_4d4e3e1f09bb781653016a0e7239855b9e4d9634', 'cs_420ad175f90fdd547fe081c38afe8042bb138528'),
(16, 'semineraliser.com', 'ck_195630773cad563ebfe4748858881508b7284754', 'cs_9e4cd4ee89c5497f48ddbd0c91b33d3ce5590ffe'),
(17, 'preprod.bystilla.com', 'ck_3f709fbd3528f1032196079d13d670428d2428df', 'cs_7ffa1b704ce128118bd2ef8c6cac73d52a6763ec'),
(18, 'home-diffusion.voyelle-dev.fr', 'ck_e62f2b8e999fb2cbbe80a2c7e4106fff1042ec61', 'cs_ae00e5c9b40de04a1147f91f4007612397f8f06d'),
(19, 'sageshop.acm.mc', 'ck_506dfe4b0dcca39f74a6e37c888a3b95ae1f1af8', 'cs_ae4d102b9d8476db7246eaf226f024e86fd48bb7'),
(20, 'www.literie-westelynck.fr', 'ck_f7f7e2124cbfb3cebf5c43840ce66f831d7003be', 'cs_d88a1b0e593b3e8e9e00078425b28f976ae9f219'),
(21, 'linwin.bartmedia.eu', 'ck_ec75a1dcef54556758e2a1ea78ab67de685e40e7', 'cs_7d37787605173b4c347296f29cf2178ce3c9ce73'),
(22, 'www.minimotors.fr', 'ck_2f782e042801bfa72c21ffa8bf026c180c99f422', 'cs_360914703f75da9d08afa308826b2e6b0a4fb900'),
(25, 'cobalt-oc.com', 'ck_f7f7e2124cbfb3cebf5c43840ce66f831d7003be', 'cs_d2786c6770f8242455b2c3862e6cf2ffa9b5c363'),
(26, 'https://shop.corpoderm.com', 'ck_366ad93bb3209311972af4c069b2d618a641f27f', 'cs_1ab81799cd70ece7145b0748fdea05bd12d2b3fa'),
(27, 'www.alsacesaveurs.com', 'ck_3d0769ec37782c85268b3fbaebd31989dfcb6c8a', 'cs_afd480bd078dfb8b7e2bb491c8556fb7916b570b'),
(29, 'boutiqueacm.com', 'ck_506dfe4b0dcca39f74a6e37c888a3b95ae1f1af8', 'cs_ae4d102b9d8476db7246eaf226f024e86fd48bb7'),
(30, 'hygiene-depot.com', 'ck_ceaadc132ae6457a1f49c98de1ab670a23f95061', 'cs_701778e9db22058d7fffc1a147be79c92299f8b2'),
(31, 'www.cotedasie.nc/dev 	', 'ck_622a33702efffd69511d5cd9d6e9b21913d26528', 'cs_d8a25ad2de116cc6791b49cad5b59bbcd1f11533'),
(32, 'boutique.epices-fuchs.fr', 'ck_75ccf0b40ea4837689e591d1f6e31032a92974ea', 'cs_aa58fb83f36730ebd53955fbdb337b43c4f659e6'),
(33, 'www.gueuledejoie.com', 'ck_b8d553c8e53a0d2a5854b65c0935abfe06558527', 'cs_815564b3d5283df9e2602908ce45509fea4c56a5'),
(34, 'www.gtj.asso.fr', 'ck_0b660b28eb83c90c9a13709501d2fb3f85df9778', 'cs_c162cc543f993316c8f68fe91cb06c0b8a416c5d'),
(35, 'maisonconstellation.com', 'ck_77cf33f59b993f9ebb40ce6de11156216c736bdf', 'cs_9805c07212b724529b46cf47ed96e8548530771b'),
(36, 'taurine-preprod.fr', 'ck_9c4f09a9ee836556444ab2aad4b2aa3d5a278700', 'cs_65aa63648d15c638c48339bccbe64dc1af8430ec'),
(37, 'www.univers-bois.com', 'ck_d4d55fa53a97194b675c3007b4f0384aa55a8a36', 'cs_3ab6aeb2415b15ead71fe58a6e09fbc618e318fb'),
(38, 'laboiteabougies.bigfamily.dev', 'ck_a5adf8e2e4c01d72d4f35ea28f3351120e925b99', 'cs_0f54324e7c2ea8c492addfee0a8928ae008f3467'),
(39, 'taurine-preprod.fr/btg-ebp', 'ck_9c4f09a9ee836556444ab2aad4b2aa3d5a278700', 'cs_65aa63648d15c638c48339bccbe64dc1af8430ec'),
(40, '6fea-531c4e1d5545.wptiger.fr', 'ck_3249d1128067698515afbefa8d7af5cefdd139a2', 'cs_5efde685f60670de3fd254ad572d5a8316e00458'),
(41, 'ci4.vaiso.net', 'ck_6d9d1b2085820321e772d8657d7f189cbbfc8f94', 'cs_5679d959eda4d086dd5986a07b4f6fed4fb895c0'),
(42, 'www.backtoglam.fr', 'ck_787136e4495ca2082eebe95f2d71f2ea344d7d17', 'cs_14e153f9eac14d25d2694c78bf6ba66ae5f6acec'),
(43, 'www.usineajeux.fr', 'ck_b1d1128783dcab6b49086047856c9f82314603b5', 'cs_7fe85abd64cb7f9408a4d44a97811d481d0d767c'),
(44, 'pantone-couleur.com', 'ck_75c1fab1d066b906cb3e9e0008f1a68e47419aea', 'cs_a744a44070f76e0c775637d9e355a5356ab84efd'),
(45, 'www.omnicuiseur.com', 'ck_858ef5e04cd7eb17041ccc7dc00bde3fd940f331', 'cs_44c62898f215681119c1bb028fcddb40fdea9744'),
(46, 'four-ephrem.com ', 'ck_3249d1128067698515afbefa8d7af5cefdd139a2', 'cs_5efde685f60670de3fd254ad572d5a8316e00458'),
(47, 'seagull.myartgomedia.com', 'ck_853d2f337e49a88aed3fc9d24c6da6c3bed9566f', 'cs_a737c4d0245031118ccc89effd474d4ccdb0f610'),
(48, 'www.viadelsol.fr', 'ck_d42bc342bb87cc7bf7ba9f57bb03210c39d6e9ce', 'cs_63c58716d6b59db0dd0f432567f0c34dcb63ead3'),
(49, 'laregledujeu.fr', 'ck_ffdaf1249f31a8bcce0910146222a5504f9ecb2d', 'cs_663de489709cc03cdb92cb747f563ac510d0be02'),
(50, 'prod-farago.dev-medialibs.fr', 'ck_93937f8c4226374c90867d9e090d7fcc42a2f96f', 'cs_300cbb120c7a557b06d8bc9c681f93b94d8ad390'),
(51, 'www.tissage-moutet.com', 'ck_7d9fc5c060b58e1a67c8af43827a015bba1a0e67', 'cs_f6da2698b6a69bb16c4f97e3be0f97f20dad2f0a');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `prestashop`
--
ALTER TABLE `prestashop`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `shopify`
--
ALTER TABLE `shopify`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `woocommerce`
--
ALTER TABLE `woocommerce`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `prestashop`
--
ALTER TABLE `prestashop`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT pour la table `shopify`
--
ALTER TABLE `shopify`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `woocommerce`
--
ALTER TABLE `woocommerce`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
