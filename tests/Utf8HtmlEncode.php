<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;

/**
 * @internal
 */
final class Utf8HtmlEncode extends \PHPUnit\Framework\TestCase
{
    public function testHtmlEncode()
    {
        $encodeData = [
            [
                'decoded' => "a\xC1b",
                'encoded' => 'a&Aacute;b',
            ],
            [
                'decoded' => 'aáb',
                'encoded' => 'a&aacute;b',
            ],
            [
                'decoded' => "a\u0102b",
                'encoded' => 'a&Abreve;b',
            ],
            [
                'decoded' => "a\u0103b",
                'encoded' => 'a&abreve;b',
            ],
            [
                'decoded' => "a\u223Eb",
                'encoded' => 'a&ac;b',
            ],
            [
                'decoded' => "a\u223Fb",
                'encoded' => 'a&acd;b',
            ],
            [
                'decoded' => "a\u223E\u0333b",
                'encoded' => 'a&acE;b',
            ],
            [
                'decoded' => "a\xC2b",
                'encoded' => 'a&Acirc;b',
            ],
            [
                'decoded' => "a\xE2b",
                'encoded' => 'a&acirc;b',
            ],
            [
                'decoded' => "a\xB4b",
                'encoded' => 'a&acute;b',
            ],
            [
                'decoded' => "a\u0410b",
                'encoded' => 'a&Acy;b',
            ],
            [
                'decoded' => "a\u0430b",
                'encoded' => 'a&acy;b',
            ],
            [
                'decoded' => "a\xC6b",
                'encoded' => 'a&AElig;b',
            ],
            [
                'decoded' => "a\xE6b",
                'encoded' => 'a&aelig;b',
            ],
            [
                'decoded' => "a\u2061b",
                'encoded' => 'a&af;b',
            ],
            [
                'decoded' => 'a𝔄b',
                'encoded' => 'a&Afr;b',
            ],
            [
                'decoded' => 'a𝔞b',
                'encoded' => 'a&afr;b',
            ],
            [
                'decoded' => "a\xC0b",
                'encoded' => 'a&Agrave;b',
            ],
            [
                'decoded' => "a\xE0b",
                'encoded' => 'a&agrave;b',
            ],
            [
                'decoded' => "a\u2135b",
                'encoded' => 'a&aleph;b',
            ],
            [
                'decoded' => "a\u0391b",
                'encoded' => 'a&Alpha;b',
            ],
            [
                'decoded' => "a\u03B1b",
                'encoded' => 'a&alpha;b',
            ],
            [
                'decoded' => "a\u0100b",
                'encoded' => 'a&Amacr;b',
            ],
            [
                'decoded' => "a\u0101b",
                'encoded' => 'a&amacr;b',
            ],
            [
                'decoded' => "a\u2A3Fb",
                'encoded' => 'a&amalg;b',
            ],
            [
                'decoded' => 'a&b',
                'encoded' => 'a&amp;b',
            ],
            [
                'decoded' => "a\u2A55b",
                'encoded' => 'a&andand;b',
            ],
            [
                'decoded' => "a\u2A53b",
                'encoded' => 'a&And;b',
            ],
            [
                'decoded' => "a\u2227b",
                'encoded' => 'a&and;b',
            ],
            [
                'decoded' => "a\u2A5Cb",
                'encoded' => 'a&andd;b',
            ],
            [
                'decoded' => "a\u2A58b",
                'encoded' => 'a&andslope;b',
            ],
            [
                'decoded' => "a\u2A5Ab",
                'encoded' => 'a&andv;b',
            ],
            [
                'decoded' => "a\u2220b",
                'encoded' => 'a&ang;b',
            ],
            [
                'decoded' => "a\u29A4b",
                'encoded' => 'a&ange;b',
            ],
            [
                'decoded' => "a\u29A8b",
                'encoded' => 'a&angmsdaa;b',
            ],
            [
                'decoded' => "a\u29A9b",
                'encoded' => 'a&angmsdab;b',
            ],
            [
                'decoded' => "a\u29AAb",
                'encoded' => 'a&angmsdac;b',
            ],
            [
                'decoded' => "a\u29ABb",
                'encoded' => 'a&angmsdad;b',
            ],
            [
                'decoded' => "a\u29ACb",
                'encoded' => 'a&angmsdae;b',
            ],
            [
                'decoded' => "a\u29ADb",
                'encoded' => 'a&angmsdaf;b',
            ],
            [
                'decoded' => "a\u29AEb",
                'encoded' => 'a&angmsdag;b',
            ],
            [
                'decoded' => "a\u29AFb",
                'encoded' => 'a&angmsdah;b',
            ],
            [
                'decoded' => "a\u2221b",
                'encoded' => 'a&angmsd;b',
            ],
            [
                'decoded' => "a\u221Fb",
                'encoded' => 'a&angrt;b',
            ],
            [
                'decoded' => "a\u22BEb",
                'encoded' => 'a&angrtvb;b',
            ],
            [
                'decoded' => "a\u299Db",
                'encoded' => 'a&angrtvbd;b',
            ],
            [
                'decoded' => "a\u2222b",
                'encoded' => 'a&angsph;b',
            ],
            [
                'decoded' => "a\xC5b",
                'encoded' => 'a&angst;b',
            ],
            [
                'decoded' => "a\u237Cb",
                'encoded' => 'a&angzarr;b',
            ],
            [
                'decoded' => "a\u0104b",
                'encoded' => 'a&Aogon;b',
            ],
            [
                'decoded' => "a\u0105b",
                'encoded' => 'a&aogon;b',
            ],
            [
                'decoded' => "a\uD835\uDD38b",
                'encoded' => 'a&Aopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD52b",
                'encoded' => 'a&aopf;b',
            ],
            [
                'decoded' => "a\u2A6Fb",
                'encoded' => 'a&apacir;b',
            ],
            [
                'decoded' => "a\u2248b",
                'encoded' => 'a&ap;b',
            ],
            [
                'decoded' => "a\u2A70b",
                'encoded' => 'a&apE;b',
            ],
            [
                'decoded' => "a\u224Ab",
                'encoded' => 'a&ape;b',
            ],
            [
                'decoded' => "a\u224Bb",
                'encoded' => 'a&apid;b',
            ],
            [
                'decoded' => "a'b",
                'encoded' => 'a&apos;b',
            ],
            [
                'decoded' => "a\xE5b",
                'encoded' => 'a&aring;b',
            ],
            [
                'decoded' => "a\uD835\uDC9Cb",
                'encoded' => 'a&Ascr;b',
            ],
            [
                'decoded' => "a\uD835\uDCB6b",
                'encoded' => 'a&ascr;b',
            ],
            [
                'decoded' => "a\xC3b",
                'encoded' => 'a&Atilde;b',
            ],
            [
                'decoded' => "a\xE3b",
                'encoded' => 'a&atilde;b',
            ],
            [
                'decoded' => "a\xC4b",
                'encoded' => 'a&Auml;b',
            ],
            [
                'decoded' => "a\xE4b",
                'encoded' => 'a&auml;b',
            ],
            [
                'decoded' => "a\u2233b",
                'encoded' => 'a&awconint;b',
            ],
            [
                'decoded' => "a\u2A11b",
                'encoded' => 'a&awint;b',
            ],
            [
                'decoded' => "a\u2AE7b",
                'encoded' => 'a&Barv;b',
            ],
            [
                'decoded' => "a\u22BDb",
                'encoded' => 'a&barvee;b',
            ],
            [
                'decoded' => "a\u2305b",
                'encoded' => 'a&barwed;b',
            ],
            [
                'decoded' => "a\u2306b",
                'encoded' => 'a&Barwed;b',
            ],
            [
                'decoded' => "a\u23B5b",
                'encoded' => 'a&bbrk;b',
            ],
            [
                'decoded' => "a\u23B6b",
                'encoded' => 'a&bbrktbrk;b',
            ],
            [
                'decoded' => "a\u224Cb",
                'encoded' => 'a&bcong;b',
            ],
            [
                'decoded' => "a\u0411b",
                'encoded' => 'a&Bcy;b',
            ],
            [
                'decoded' => "a\u0431b",
                'encoded' => 'a&bcy;b',
            ],
            [
                'decoded' => "a\u201Eb",
                'encoded' => 'a&bdquo;b',
            ],
            [
                'decoded' => "a\u2235b",
                'encoded' => 'a&becaus;b',
            ],
            [
                'decoded' => "a\u29B0b",
                'encoded' => 'a&bemptyv;b',
            ],
            [
                'decoded' => "a\u03F6b",
                'encoded' => 'a&bepsi;b',
            ],
            [
                'decoded' => "a\u0392b",
                'encoded' => 'a&Beta;b',
            ],
            [
                'decoded' => "a\u03B2b",
                'encoded' => 'a&beta;b',
            ],
            [
                'decoded' => "a\u2136b",
                'encoded' => 'a&beth;b',
            ],
            [
                'decoded' => "a\uD835\uDD05b",
                'encoded' => 'a&Bfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD1Fb",
                'encoded' => 'a&bfr;b',
            ],
            [
                'decoded' => "a\u2423b",
                'encoded' => 'a&blank;b',
            ],
            [
                'decoded' => "a\u2592b",
                'encoded' => 'a&blk12;b',
            ],
            [
                'decoded' => "a\u2591b",
                'encoded' => 'a&blk14;b',
            ],
            [
                'decoded' => "a\u2593b",
                'encoded' => 'a&blk34;b',
            ],
            [
                'decoded' => "a\u2588b",
                'encoded' => 'a&block;b',
            ],
            [
                'decoded' => "a=\u20E5b",
                'encoded' => 'a&bne;b',
            ],
            [
                'decoded' => "a\u2261\u20E5b",
                'encoded' => 'a&bnequiv;b',
            ],
            [
                'decoded' => "a\u2AEDb",
                'encoded' => 'a&bNot;b',
            ],
            [
                'decoded' => "a\u2310b",
                'encoded' => 'a&bnot;b',
            ],
            [
                'decoded' => "a\uD835\uDD39b",
                'encoded' => 'a&Bopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD53b",
                'encoded' => 'a&bopf;b',
            ],
            [
                'decoded' => "a\u22A5b",
                'encoded' => 'a&bot;b',
            ],
            [
                'decoded' => "a\u22C8b",
                'encoded' => 'a&bowtie;b',
            ],
            [
                'decoded' => "a\u29C9b",
                'encoded' => 'a&boxbox;b',
            ],
            [
                'decoded' => "a\u2510b",
                'encoded' => 'a&boxdl;b',
            ],
            [
                'decoded' => "a\u2555b",
                'encoded' => 'a&boxdL;b',
            ],
            [
                'decoded' => "a\u2556b",
                'encoded' => 'a&boxDl;b',
            ],
            [
                'decoded' => "a\u2557b",
                'encoded' => 'a&boxDL;b',
            ],
            [
                'decoded' => "a\u250Cb",
                'encoded' => 'a&boxdr;b',
            ],
            [
                'decoded' => "a\u2552b",
                'encoded' => 'a&boxdR;b',
            ],
            [
                'decoded' => "a\u2553b",
                'encoded' => 'a&boxDr;b',
            ],
            [
                'decoded' => "a\u2554b",
                'encoded' => 'a&boxDR;b',
            ],
            [
                'decoded' => "a\u2500b",
                'encoded' => 'a&boxh;b',
            ],
            [
                'decoded' => "a\u2550b",
                'encoded' => 'a&boxH;b',
            ],
            [
                'decoded' => "a\u252Cb",
                'encoded' => 'a&boxhd;b',
            ],
            [
                'decoded' => "a\u2564b",
                'encoded' => 'a&boxHd;b',
            ],
            [
                'decoded' => "a\u2565b",
                'encoded' => 'a&boxhD;b',
            ],
            [
                'decoded' => "a\u2566b",
                'encoded' => 'a&boxHD;b',
            ],
            [
                'decoded' => "a\u2534b",
                'encoded' => 'a&boxhu;b',
            ],
            [
                'decoded' => "a\u2567b",
                'encoded' => 'a&boxHu;b',
            ],
            [
                'decoded' => "a\u2568b",
                'encoded' => 'a&boxhU;b',
            ],
            [
                'decoded' => "a\u2569b",
                'encoded' => 'a&boxHU;b',
            ],
            [
                'decoded' => "a\u2518b",
                'encoded' => 'a&boxul;b',
            ],
            [
                'decoded' => "a\u255Bb",
                'encoded' => 'a&boxuL;b',
            ],
            [
                'decoded' => "a\u255Cb",
                'encoded' => 'a&boxUl;b',
            ],
            [
                'decoded' => "a\u255Db",
                'encoded' => 'a&boxUL;b',
            ],
            [
                'decoded' => "a\u2514b",
                'encoded' => 'a&boxur;b',
            ],
            [
                'decoded' => "a\u2558b",
                'encoded' => 'a&boxuR;b',
            ],
            [
                'decoded' => "a\u2559b",
                'encoded' => 'a&boxUr;b',
            ],
            [
                'decoded' => "a\u255Ab",
                'encoded' => 'a&boxUR;b',
            ],
            [
                'decoded' => "a\u2502b",
                'encoded' => 'a&boxv;b',
            ],
            [
                'decoded' => "a\u2551b",
                'encoded' => 'a&boxV;b',
            ],
            [
                'decoded' => "a\u253Cb",
                'encoded' => 'a&boxvh;b',
            ],
            [
                'decoded' => "a\u256Ab",
                'encoded' => 'a&boxvH;b',
            ],
            [
                'decoded' => "a\u256Bb",
                'encoded' => 'a&boxVh;b',
            ],
            [
                'decoded' => "a\u256Cb",
                'encoded' => 'a&boxVH;b',
            ],
            [
                'decoded' => "a\u2524b",
                'encoded' => 'a&boxvl;b',
            ],
            [
                'decoded' => "a\u2561b",
                'encoded' => 'a&boxvL;b',
            ],
            [
                'decoded' => "a\u2562b",
                'encoded' => 'a&boxVl;b',
            ],
            [
                'decoded' => "a\u2563b",
                'encoded' => 'a&boxVL;b',
            ],
            [
                'decoded' => "a\u251Cb",
                'encoded' => 'a&boxvr;b',
            ],
            [
                'decoded' => "a\u255Eb",
                'encoded' => 'a&boxvR;b',
            ],
            [
                'decoded' => "a\u255Fb",
                'encoded' => 'a&boxVr;b',
            ],
            [
                'decoded' => "a\u2560b",
                'encoded' => 'a&boxVR;b',
            ],
            [
                'decoded' => "a\u2035b",
                'encoded' => 'a&bprime;b',
            ],
            [
                'decoded' => "a\u02D8b",
                'encoded' => 'a&breve;b',
            ],
            [
                'decoded' => "a\xA6b",
                'encoded' => 'a&brvbar;b',
            ],
            [
                'decoded' => "a\uD835\uDCB7b",
                'encoded' => 'a&bscr;b',
            ],
            [
                'decoded' => "a\u212Cb",
                'encoded' => 'a&Bscr;b',
            ],
            [
                'decoded' => "a\u204Fb",
                'encoded' => 'a&bsemi;b',
            ],
            [
                'decoded' => "a\u223Db",
                'encoded' => 'a&bsim;b',
            ],
            [
                'decoded' => "a\u22CDb",
                'encoded' => 'a&bsime;b',
            ],
            [
                'decoded' => "a\u29C5b",
                'encoded' => 'a&bsolb;b',
            ],
            [
                'decoded' => "a\u27C8b",
                'encoded' => 'a&bsolhsub;b',
            ],
            [
                'decoded' => "a\u2022b",
                'encoded' => 'a&bull;b',
            ],
            [
                'decoded' => "a\u224Eb",
                'encoded' => 'a&bump;b',
            ],
            [
                'decoded' => "a\u2AAEb",
                'encoded' => 'a&bumpE;b',
            ],
            [
                'decoded' => "a\u224Fb",
                'encoded' => 'a&bumpe;b',
            ],
            [
                'decoded' => "a\u0106b",
                'encoded' => 'a&Cacute;b',
            ],
            [
                'decoded' => "a\u0107b",
                'encoded' => 'a&cacute;b',
            ],
            [
                'decoded' => "a\u2A44b",
                'encoded' => 'a&capand;b',
            ],
            [
                'decoded' => "a\u2A49b",
                'encoded' => 'a&capbrcup;b',
            ],
            [
                'decoded' => "a\u2A4Bb",
                'encoded' => 'a&capcap;b',
            ],
            [
                'decoded' => "a\u2229b",
                'encoded' => 'a&cap;b',
            ],
            [
                'decoded' => "a\u22D2b",
                'encoded' => 'a&Cap;b',
            ],
            [
                'decoded' => "a\u2A47b",
                'encoded' => 'a&capcup;b',
            ],
            [
                'decoded' => "a\u2A40b",
                'encoded' => 'a&capdot;b',
            ],
            [
                'decoded' => "a\u2229\uFE00b",
                'encoded' => 'a&caps;b',
            ],
            [
                'decoded' => "a\u2041b",
                'encoded' => 'a&caret;b',
            ],
            [
                'decoded' => "a\u02C7b",
                'encoded' => 'a&caron;b',
            ],
            [
                'decoded' => "a\u2A4Db",
                'encoded' => 'a&ccaps;b',
            ],
            [
                'decoded' => "a\u010Cb",
                'encoded' => 'a&Ccaron;b',
            ],
            [
                'decoded' => "a\u010Db",
                'encoded' => 'a&ccaron;b',
            ],
            [
                'decoded' => "a\xC7b",
                'encoded' => 'a&Ccedil;b',
            ],
            [
                'decoded' => "a\xE7b",
                'encoded' => 'a&ccedil;b',
            ],
            [
                'decoded' => "a\u0108b",
                'encoded' => 'a&Ccirc;b',
            ],
            [
                'decoded' => "a\u0109b",
                'encoded' => 'a&ccirc;b',
            ],
            [
                'decoded' => "a\u2230b",
                'encoded' => 'a&Cconint;b',
            ],
            [
                'decoded' => "a\u2A4Cb",
                'encoded' => 'a&ccups;b',
            ],
            [
                'decoded' => "a\u2A50b",
                'encoded' => 'a&ccupssm;b',
            ],
            [
                'decoded' => "a\u010Ab",
                'encoded' => 'a&Cdot;b',
            ],
            [
                'decoded' => "a\u010Bb",
                'encoded' => 'a&cdot;b',
            ],
            [
                'decoded' => "a\xB8b",
                'encoded' => 'a&cedil;b',
            ],
            [
                'decoded' => "a\u29B2b",
                'encoded' => 'a&cemptyv;b',
            ],
            [
                'decoded' => "a\xA2b",
                'encoded' => 'a&cent;b',
            ],
            [
                'decoded' => "a\uD835\uDD20b",
                'encoded' => 'a&cfr;b',
            ],
            [
                'decoded' => "a\u212Db",
                'encoded' => 'a&Cfr;b',
            ],
            [
                'decoded' => "a\u0427b",
                'encoded' => 'a&CHcy;b',
            ],
            [
                'decoded' => "a\u0447b",
                'encoded' => 'a&chcy;b',
            ],
            [
                'decoded' => "a\u2713b",
                'encoded' => 'a&check;b',
            ],
            [
                'decoded' => "a\u03A7b",
                'encoded' => 'a&Chi;b',
            ],
            [
                'decoded' => "a\u03C7b",
                'encoded' => 'a&chi;b',
            ],
            [
                'decoded' => "a\u02C6b",
                'encoded' => 'a&circ;b',
            ],
            [
                'decoded' => "a\u25CBb",
                'encoded' => 'a&cir;b',
            ],
            [
                'decoded' => "a\u29C3b",
                'encoded' => 'a&cirE;b',
            ],
            [
                'decoded' => "a\u2257b",
                'encoded' => 'a&cire;b',
            ],
            [
                'decoded' => "a\u2A10b",
                'encoded' => 'a&cirfnint;b',
            ],
            [
                'decoded' => "a\u2AEFb",
                'encoded' => 'a&cirmid;b',
            ],
            [
                'decoded' => "a\u29C2b",
                'encoded' => 'a&cirscir;b',
            ],
            [
                'decoded' => "a\u2663b",
                'encoded' => 'a&clubs;b',
            ],
            [
                'decoded' => "a\u2237b",
                'encoded' => 'a&Colon;b',
            ],
            [
                'decoded' => "a\u2A74b",
                'encoded' => 'a&Colone;b',
            ],
            [
                'decoded' => "a\u2254b",
                'encoded' => 'a&colone;b',
            ],
            [
                'decoded' => "a\u2201b",
                'encoded' => 'a&comp;b',
            ],
            [
                'decoded' => "a\u2218b",
                'encoded' => 'a&compfn;b',
            ],
            [
                'decoded' => "a\u2245b",
                'encoded' => 'a&cong;b',
            ],
            [
                'decoded' => "a\u2A6Db",
                'encoded' => 'a&congdot;b',
            ],
            [
                'decoded' => "a\u222Fb",
                'encoded' => 'a&Conint;b',
            ],
            [
                'decoded' => "a\uD835\uDD54b",
                'encoded' => 'a&copf;b',
            ],
            [
                'decoded' => "a\u2102b",
                'encoded' => 'a&Copf;b',
            ],
            [
                'decoded' => "a\u2210b",
                'encoded' => 'a&coprod;b',
            ],
            [
                'decoded' => "a\xA9b",
                'encoded' => 'a&copy;b',
            ],
            [
                'decoded' => "a\u2117b",
                'encoded' => 'a&copysr;b',
            ],
            [
                'decoded' => "a\u21B5b",
                'encoded' => 'a&crarr;b',
            ],
            [
                'decoded' => "a\u2717b",
                'encoded' => 'a&cross;b',
            ],
            [
                'decoded' => "a\u2A2Fb",
                'encoded' => 'a&Cross;b',
            ],
            [
                'decoded' => "a\uD835\uDC9Eb",
                'encoded' => 'a&Cscr;b',
            ],
            [
                'decoded' => "a\uD835\uDCB8b",
                'encoded' => 'a&cscr;b',
            ],
            [
                'decoded' => "a\u2ACFb",
                'encoded' => 'a&csub;b',
            ],
            [
                'decoded' => "a\u2AD1b",
                'encoded' => 'a&csube;b',
            ],
            [
                'decoded' => "a\u2AD0b",
                'encoded' => 'a&csup;b',
            ],
            [
                'decoded' => "a\u2AD2b",
                'encoded' => 'a&csupe;b',
            ],
            [
                'decoded' => "a\u22EFb",
                'encoded' => 'a&ctdot;b',
            ],
            [
                'decoded' => "a\u2938b",
                'encoded' => 'a&cudarrl;b',
            ],
            [
                'decoded' => "a\u2935b",
                'encoded' => 'a&cudarrr;b',
            ],
            [
                'decoded' => "a\u22DEb",
                'encoded' => 'a&cuepr;b',
            ],
            [
                'decoded' => "a\u22DFb",
                'encoded' => 'a&cuesc;b',
            ],
            [
                'decoded' => "a\u21B6b",
                'encoded' => 'a&cularr;b',
            ],
            [
                'decoded' => "a\u293Db",
                'encoded' => 'a&cularrp;b',
            ],
            [
                'decoded' => "a\u2A48b",
                'encoded' => 'a&cupbrcap;b',
            ],
            [
                'decoded' => "a\u2A46b",
                'encoded' => 'a&cupcap;b',
            ],
            [
                'decoded' => "a\u224Db",
                'encoded' => 'a&CupCap;b',
            ],
            [
                'decoded' => "a\u222Ab",
                'encoded' => 'a&cup;b',
            ],
            [
                'decoded' => "a\u22D3b",
                'encoded' => 'a&Cup;b',
            ],
            [
                'decoded' => "a\u2A4Ab",
                'encoded' => 'a&cupcup;b',
            ],
            [
                'decoded' => "a\u228Db",
                'encoded' => 'a&cupdot;b',
            ],
            [
                'decoded' => "a\u2A45b",
                'encoded' => 'a&cupor;b',
            ],
            [
                'decoded' => "a\u222A\uFE00b",
                'encoded' => 'a&cups;b',
            ],
            [
                'decoded' => "a\u21B7b",
                'encoded' => 'a&curarr;b',
            ],
            [
                'decoded' => "a\u293Cb",
                'encoded' => 'a&curarrm;b',
            ],
            [
                'decoded' => "a\xA4b",
                'encoded' => 'a&curren;b',
            ],
            [
                'decoded' => "a\u22CEb",
                'encoded' => 'a&cuvee;b',
            ],
            [
                'decoded' => "a\u22CFb",
                'encoded' => 'a&cuwed;b',
            ],
            [
                'decoded' => "a\u2232b",
                'encoded' => 'a&cwconint;b',
            ],
            [
                'decoded' => "a\u2231b",
                'encoded' => 'a&cwint;b',
            ],
            [
                'decoded' => "a\u232Db",
                'encoded' => 'a&cylcty;b',
            ],
            [
                'decoded' => "a\u2020b",
                'encoded' => 'a&dagger;b',
            ],
            [
                'decoded' => "a\u2021b",
                'encoded' => 'a&Dagger;b',
            ],
            [
                'decoded' => "a\u2138b",
                'encoded' => 'a&daleth;b',
            ],
            [
                'decoded' => "a\u2193b",
                'encoded' => 'a&darr;b',
            ],
            [
                'decoded' => "a\u21A1b",
                'encoded' => 'a&Darr;b',
            ],
            [
                'decoded' => "a\u21D3b",
                'encoded' => 'a&dArr;b',
            ],
            [
                'decoded' => "a\u2010b",
                'encoded' => 'a&dash;b',
            ],
            [
                'decoded' => "a\u2AE4b",
                'encoded' => 'a&Dashv;b',
            ],
            [
                'decoded' => "a\u22A3b",
                'encoded' => 'a&dashv;b',
            ],
            [
                'decoded' => "a\u02DDb",
                'encoded' => 'a&dblac;b',
            ],
            [
                'decoded' => "a\u010Eb",
                'encoded' => 'a&Dcaron;b',
            ],
            [
                'decoded' => "a\u010Fb",
                'encoded' => 'a&dcaron;b',
            ],
            [
                'decoded' => "a\u0414b",
                'encoded' => 'a&Dcy;b',
            ],
            [
                'decoded' => "a\u0434b",
                'encoded' => 'a&dcy;b',
            ],
            [
                'decoded' => "a\u21CAb",
                'encoded' => 'a&ddarr;b',
            ],
            [
                'decoded' => "a\u2145b",
                'encoded' => 'a&DD;b',
            ],
            [
                'decoded' => "a\u2146b",
                'encoded' => 'a&dd;b',
            ],
            [
                'decoded' => "a\u2911b",
                'encoded' => 'a&DDotrahd;b',
            ],
            [
                'decoded' => "a\xB0b",
                'encoded' => 'a&deg;b',
            ],
            [
                'decoded' => "a\u2207b",
                'encoded' => 'a&Del;b',
            ],
            [
                'decoded' => "a\u0394b",
                'encoded' => 'a&Delta;b',
            ],
            [
                'decoded' => "a\u03B4b",
                'encoded' => 'a&delta;b',
            ],
            [
                'decoded' => "a\u29B1b",
                'encoded' => 'a&demptyv;b',
            ],
            [
                'decoded' => "a\u297Fb",
                'encoded' => 'a&dfisht;b',
            ],
            [
                'decoded' => "a\uD835\uDD07b",
                'encoded' => 'a&Dfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD21b",
                'encoded' => 'a&dfr;b',
            ],
            [
                'decoded' => "a\u2965b",
                'encoded' => 'a&dHar;b',
            ],
            [
                'decoded' => "a\u21C3b",
                'encoded' => 'a&dharl;b',
            ],
            [
                'decoded' => "a\u21C2b",
                'encoded' => 'a&dharr;b',
            ],
            [
                'decoded' => "a\u22C4b",
                'encoded' => 'a&diam;b',
            ],
            [
                'decoded' => "a\u2666b",
                'encoded' => 'a&diams;b',
            ],
            [
                'decoded' => "a\xA8b",
                'encoded' => 'a&die;b',
            ],
            [
                'decoded' => "a\u22F2b",
                'encoded' => 'a&disin;b',
            ],
            [
                'decoded' => "a\xF7b",
                'encoded' => 'a&div;b',
            ],
            [
                'decoded' => "a\u22C7b",
                'encoded' => 'a&divonx;b',
            ],
            [
                'decoded' => "a\u0402b",
                'encoded' => 'a&DJcy;b',
            ],
            [
                'decoded' => "a\u0452b",
                'encoded' => 'a&djcy;b',
            ],
            [
                'decoded' => "a\u231Eb",
                'encoded' => 'a&dlcorn;b',
            ],
            [
                'decoded' => "a\u230Db",
                'encoded' => 'a&dlcrop;b',
            ],
            [
                'decoded' => "a\uD835\uDD3Bb",
                'encoded' => 'a&Dopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD55b",
                'encoded' => 'a&dopf;b',
            ],
            [
                'decoded' => "a\u02D9b",
                'encoded' => 'a&dot;b',
            ],
            [
                'decoded' => "a\u20DCb",
                'encoded' => 'a&DotDot;b',
            ],
            [
                'decoded' => "a\u2250b",
                'encoded' => 'a&doteq;b',
            ],
            [
                'decoded' => "a\u2913b",
                'encoded' => 'a&DownArrowBar;b',
            ],
            [
                'decoded' => "a\u0311b",
                'encoded' => 'a&DownBreve;b',
            ],
            [
                'decoded' => "a\u2950b",
                'encoded' => 'a&DownLeftRightVector;b',
            ],
            [
                'decoded' => "a\u295Eb",
                'encoded' => 'a&DownLeftTeeVector;b',
            ],
            [
                'decoded' => "a\u2956b",
                'encoded' => 'a&DownLeftVectorBar;b',
            ],
            [
                'decoded' => "a\u295Fb",
                'encoded' => 'a&DownRightTeeVector;b',
            ],
            [
                'decoded' => "a\u2957b",
                'encoded' => 'a&DownRightVectorBar;b',
            ],
            [
                'decoded' => "a\u231Fb",
                'encoded' => 'a&drcorn;b',
            ],
            [
                'decoded' => "a\u230Cb",
                'encoded' => 'a&drcrop;b',
            ],
            [
                'decoded' => "a\uD835\uDC9Fb",
                'encoded' => 'a&Dscr;b',
            ],
            [
                'decoded' => "a\uD835\uDCB9b",
                'encoded' => 'a&dscr;b',
            ],
            [
                'decoded' => "a\u0405b",
                'encoded' => 'a&DScy;b',
            ],
            [
                'decoded' => "a\u0455b",
                'encoded' => 'a&dscy;b',
            ],
            [
                'decoded' => "a\u29F6b",
                'encoded' => 'a&dsol;b',
            ],
            [
                'decoded' => "a\u0110b",
                'encoded' => 'a&Dstrok;b',
            ],
            [
                'decoded' => "a\u0111b",
                'encoded' => 'a&dstrok;b',
            ],
            [
                'decoded' => "a\u22F1b",
                'encoded' => 'a&dtdot;b',
            ],
            [
                'decoded' => "a\u25BFb",
                'encoded' => 'a&dtri;b',
            ],
            [
                'decoded' => "a\u25BEb",
                'encoded' => 'a&dtrif;b',
            ],
            [
                'decoded' => "a\u21F5b",
                'encoded' => 'a&duarr;b',
            ],
            [
                'decoded' => "a\u296Fb",
                'encoded' => 'a&duhar;b',
            ],
            [
                'decoded' => "a\u29A6b",
                'encoded' => 'a&dwangle;b',
            ],
            [
                'decoded' => "a\u040Fb",
                'encoded' => 'a&DZcy;b',
            ],
            [
                'decoded' => "a\u045Fb",
                'encoded' => 'a&dzcy;b',
            ],
            [
                'decoded' => "a\u27FFb",
                'encoded' => 'a&dzigrarr;b',
            ],
            [
                'decoded' => "a\xC9b",
                'encoded' => 'a&Eacute;b',
            ],
            [
                'decoded' => "a\xE9b",
                'encoded' => 'a&eacute;b',
            ],
            [
                'decoded' => "a\u2A6Eb",
                'encoded' => 'a&easter;b',
            ],
            [
                'decoded' => "a\u011Ab",
                'encoded' => 'a&Ecaron;b',
            ],
            [
                'decoded' => "a\u011Bb",
                'encoded' => 'a&ecaron;b',
            ],
            [
                'decoded' => "a\xCAb",
                'encoded' => 'a&Ecirc;b',
            ],
            [
                'decoded' => "a\xEAb",
                'encoded' => 'a&ecirc;b',
            ],
            [
                'decoded' => "a\u2256b",
                'encoded' => 'a&ecir;b',
            ],
            [
                'decoded' => "a\u2255b",
                'encoded' => 'a&ecolon;b',
            ],
            [
                'decoded' => "a\u042Db",
                'encoded' => 'a&Ecy;b',
            ],
            [
                'decoded' => "a\u044Db",
                'encoded' => 'a&ecy;b',
            ],
            [
                'decoded' => "a\u2A77b",
                'encoded' => 'a&eDDot;b',
            ],
            [
                'decoded' => "a\u0116b",
                'encoded' => 'a&Edot;b',
            ],
            [
                'decoded' => "a\u0117b",
                'encoded' => 'a&edot;b',
            ],
            [
                'decoded' => "a\u2251b",
                'encoded' => 'a&eDot;b',
            ],
            [
                'decoded' => "a\u2147b",
                'encoded' => 'a&ee;b',
            ],
            [
                'decoded' => "a\u2252b",
                'encoded' => 'a&efDot;b',
            ],
            [
                'decoded' => "a\uD835\uDD08b",
                'encoded' => 'a&Efr;b',
            ],
            [
                'decoded' => "a\uD835\uDD22b",
                'encoded' => 'a&efr;b',
            ],
            [
                'decoded' => "a\u2A9Ab",
                'encoded' => 'a&eg;b',
            ],
            [
                'decoded' => "a\xC8b",
                'encoded' => 'a&Egrave;b',
            ],
            [
                'decoded' => "a\xE8b",
                'encoded' => 'a&egrave;b',
            ],
            [
                'decoded' => "a\u2A96b",
                'encoded' => 'a&egs;b',
            ],
            [
                'decoded' => "a\u2A98b",
                'encoded' => 'a&egsdot;b',
            ],
            [
                'decoded' => "a\u2A99b",
                'encoded' => 'a&el;b',
            ],
            [
                'decoded' => "a\u23E7b",
                'encoded' => 'a&elinters;b',
            ],
            [
                'decoded' => "a\u2113b",
                'encoded' => 'a&ell;b',
            ],
            [
                'decoded' => "a\u2A95b",
                'encoded' => 'a&els;b',
            ],
            [
                'decoded' => "a\u2A97b",
                'encoded' => 'a&elsdot;b',
            ],
            [
                'decoded' => "a\u0112b",
                'encoded' => 'a&Emacr;b',
            ],
            [
                'decoded' => "a\u0113b",
                'encoded' => 'a&emacr;b',
            ],
            [
                'decoded' => "a\u2205b",
                'encoded' => 'a&empty;b',
            ],
            [
                'decoded' => "a\u25FBb",
                'encoded' => 'a&EmptySmallSquare;b',
            ],
            [
                'decoded' => "a\u25ABb",
                'encoded' => 'a&EmptyVerySmallSquare;b',
            ],
            [
                'decoded' => "a\u2004b",
                'encoded' => 'a&emsp13;b',
            ],
            [
                'decoded' => "a\u2005b",
                'encoded' => 'a&emsp14;b',
            ],
            [
                'decoded' => "a\u2003b",
                'encoded' => 'a&emsp;b',
            ],
            [
                'decoded' => "a\u014Ab",
                'encoded' => 'a&ENG;b',
            ],
            [
                'decoded' => "a\u014Bb",
                'encoded' => 'a&eng;b',
            ],
            [
                'decoded' => "a\u2002b",
                'encoded' => 'a&ensp;b',
            ],
            [
                'decoded' => "a\u0118b",
                'encoded' => 'a&Eogon;b',
            ],
            [
                'decoded' => "a\u0119b",
                'encoded' => 'a&eogon;b',
            ],
            [
                'decoded' => "a\uD835\uDD3Cb",
                'encoded' => 'a&Eopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD56b",
                'encoded' => 'a&eopf;b',
            ],
            [
                'decoded' => "a\u22D5b",
                'encoded' => 'a&epar;b',
            ],
            [
                'decoded' => "a\u29E3b",
                'encoded' => 'a&eparsl;b',
            ],
            [
                'decoded' => "a\u2A71b",
                'encoded' => 'a&eplus;b',
            ],
            [
                'decoded' => "a\u03B5b",
                'encoded' => 'a&epsi;b',
            ],
            [
                'decoded' => "a\u0395b",
                'encoded' => 'a&Epsilon;b',
            ],
            [
                'decoded' => "a\u03F5b",
                'encoded' => 'a&epsiv;b',
            ],
            [
                'decoded' => "a\u2A75b",
                'encoded' => 'a&Equal;b',
            ],
            [
                'decoded' => "a\u2261b",
                'encoded' => 'a&equiv;b',
            ],
            [
                'decoded' => "a\u2A78b",
                'encoded' => 'a&equivDD;b',
            ],
            [
                'decoded' => "a\u29E5b",
                'encoded' => 'a&eqvparsl;b',
            ],
            [
                'decoded' => "a\u2971b",
                'encoded' => 'a&erarr;b',
            ],
            [
                'decoded' => "a\u2253b",
                'encoded' => 'a&erDot;b',
            ],
            [
                'decoded' => "a\u212Fb",
                'encoded' => 'a&escr;b',
            ],
            [
                'decoded' => "a\u2130b",
                'encoded' => 'a&Escr;b',
            ],
            [
                'decoded' => "a\u2A73b",
                'encoded' => 'a&Esim;b',
            ],
            [
                'decoded' => "a\u2242b",
                'encoded' => 'a&esim;b',
            ],
            [
                'decoded' => "a\u0397b",
                'encoded' => 'a&Eta;b',
            ],
            [
                'decoded' => "a\u03B7b",
                'encoded' => 'a&eta;b',
            ],
            [
                'decoded' => "a\xD0b",
                'encoded' => 'a&ETH;b',
            ],
            [
                'decoded' => "a\xF0b",
                'encoded' => 'a&eth;b',
            ],
            [
                'decoded' => "a\xCBb",
                'encoded' => 'a&Euml;b',
            ],
            [
                'decoded' => "a\xEBb",
                'encoded' => 'a&euml;b',
            ],
            [
                'decoded' => "a\u20ACb",
                'encoded' => 'a&euro;b',
            ],
            [
                'decoded' => "a\u2203b",
                'encoded' => 'a&exist;b',
            ],
            [
                'decoded' => "a\u0424b",
                'encoded' => 'a&Fcy;b',
            ],
            [
                'decoded' => "a\u0444b",
                'encoded' => 'a&fcy;b',
            ],
            [
                'decoded' => "a\u2640b",
                'encoded' => 'a&female;b',
            ],
            [
                'decoded' => "a\uFB03b",
                'encoded' => 'a&ffilig;b',
            ],
            [
                'decoded' => "a\uFB00b",
                'encoded' => 'a&fflig;b',
            ],
            [
                'decoded' => "a\uFB04b",
                'encoded' => 'a&ffllig;b',
            ],
            [
                'decoded' => "a\uD835\uDD09b",
                'encoded' => 'a&Ffr;b',
            ],
            [
                'decoded' => "a\uD835\uDD23b",
                'encoded' => 'a&ffr;b',
            ],
            [
                'decoded' => "a\uFB01b",
                'encoded' => 'a&filig;b',
            ],
            [
                'decoded' => "a\u25FCb",
                'encoded' => 'a&FilledSmallSquare;b',
            ],
            [
                'decoded' => "a\u266Db",
                'encoded' => 'a&flat;b',
            ],
            [
                'decoded' => "a\uFB02b",
                'encoded' => 'a&fllig;b',
            ],
            [
                'decoded' => "a\u25B1b",
                'encoded' => 'a&fltns;b',
            ],
            [
                'decoded' => "a\u0192b",
                'encoded' => 'a&fnof;b',
            ],
            [
                'decoded' => "a\uD835\uDD3Db",
                'encoded' => 'a&Fopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD57b",
                'encoded' => 'a&fopf;b',
            ],
            [
                'decoded' => "a\u2200b",
                'encoded' => 'a&forall;b',
            ],
            [
                'decoded' => "a\u22D4b",
                'encoded' => 'a&fork;b',
            ],
            [
                'decoded' => "a\u2AD9b",
                'encoded' => 'a&forkv;b',
            ],
            [
                'decoded' => "a\u2A0Db",
                'encoded' => 'a&fpartint;b',
            ],
            [
                'decoded' => "a\u2153b",
                'encoded' => 'a&frac13;b',
            ],
            [
                'decoded' => "a\xBCb",
                'encoded' => 'a&frac14;b',
            ],
            [
                'decoded' => "a\u2155b",
                'encoded' => 'a&frac15;b',
            ],
            [
                'decoded' => "a\u2159b",
                'encoded' => 'a&frac16;b',
            ],
            [
                'decoded' => "a\u215Bb",
                'encoded' => 'a&frac18;b',
            ],
            [
                'decoded' => "a\u2154b",
                'encoded' => 'a&frac23;b',
            ],
            [
                'decoded' => "a\u2156b",
                'encoded' => 'a&frac25;b',
            ],
            [
                'decoded' => "a\xBEb",
                'encoded' => 'a&frac34;b',
            ],
            [
                'decoded' => "a\u2157b",
                'encoded' => 'a&frac35;b',
            ],
            [
                'decoded' => "a\u215Cb",
                'encoded' => 'a&frac38;b',
            ],
            [
                'decoded' => "a\u2158b",
                'encoded' => 'a&frac45;b',
            ],
            [
                'decoded' => "a\u215Ab",
                'encoded' => 'a&frac56;b',
            ],
            [
                'decoded' => "a\u215Db",
                'encoded' => 'a&frac58;b',
            ],
            [
                'decoded' => "a\u215Eb",
                'encoded' => 'a&frac78;b',
            ],
            [
                'decoded' => "a\u2044b",
                'encoded' => 'a&frasl;b',
            ],
            [
                'decoded' => "a\u2322b",
                'encoded' => 'a&frown;b',
            ],
            [
                'decoded' => "a\uD835\uDCBBb",
                'encoded' => 'a&fscr;b',
            ],
            [
                'decoded' => "a\u2131b",
                'encoded' => 'a&Fscr;b',
            ],
            [
                'decoded' => "a\u01F5b",
                'encoded' => 'a&gacute;b',
            ],
            [
                'decoded' => "a\u0393b",
                'encoded' => 'a&Gamma;b',
            ],
            [
                'decoded' => "a\u03B3b",
                'encoded' => 'a&gamma;b',
            ],
            [
                'decoded' => "a\u03DCb",
                'encoded' => 'a&Gammad;b',
            ],
            [
                'decoded' => "a\u03DDb",
                'encoded' => 'a&gammad;b',
            ],
            [
                'decoded' => "a\u2A86b",
                'encoded' => 'a&gap;b',
            ],
            [
                'decoded' => "a\u011Eb",
                'encoded' => 'a&Gbreve;b',
            ],
            [
                'decoded' => "a\u011Fb",
                'encoded' => 'a&gbreve;b',
            ],
            [
                'decoded' => "a\u0122b",
                'encoded' => 'a&Gcedil;b',
            ],
            [
                'decoded' => "a\u011Cb",
                'encoded' => 'a&Gcirc;b',
            ],
            [
                'decoded' => "a\u011Db",
                'encoded' => 'a&gcirc;b',
            ],
            [
                'decoded' => "a\u0413b",
                'encoded' => 'a&Gcy;b',
            ],
            [
                'decoded' => "a\u0433b",
                'encoded' => 'a&gcy;b',
            ],
            [
                'decoded' => "a\u0120b",
                'encoded' => 'a&Gdot;b',
            ],
            [
                'decoded' => "a\u0121b",
                'encoded' => 'a&gdot;b',
            ],
            [
                'decoded' => "a\u2265b",
                'encoded' => 'a&ge;b',
            ],
            [
                'decoded' => "a\u2267b",
                'encoded' => 'a&gE;b',
            ],
            [
                'decoded' => "a\u2A8Cb",
                'encoded' => 'a&gEl;b',
            ],
            [
                'decoded' => "a\u22DBb",
                'encoded' => 'a&gel;b',
            ],
            [
                'decoded' => "a\u2AA9b",
                'encoded' => 'a&gescc;b',
            ],
            [
                'decoded' => "a\u2A7Eb",
                'encoded' => 'a&ges;b',
            ],
            [
                'decoded' => "a\u2A80b",
                'encoded' => 'a&gesdot;b',
            ],
            [
                'decoded' => "a\u2A82b",
                'encoded' => 'a&gesdoto;b',
            ],
            [
                'decoded' => "a\u2A84b",
                'encoded' => 'a&gesdotol;b',
            ],
            [
                'decoded' => "a\u22DB\uFE00b",
                'encoded' => 'a&gesl;b',
            ],
            [
                'decoded' => "a\u2A94b",
                'encoded' => 'a&gesles;b',
            ],
            [
                'decoded' => "a\uD835\uDD0Ab",
                'encoded' => 'a&Gfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD24b",
                'encoded' => 'a&gfr;b',
            ],
            [
                'decoded' => "a\u226Bb",
                'encoded' => 'a&gg;b',
            ],
            [
                'decoded' => "a\u22D9b",
                'encoded' => 'a&Gg;b',
            ],
            [
                'decoded' => "a\u2137b",
                'encoded' => 'a&gimel;b',
            ],
            [
                'decoded' => "a\u0403b",
                'encoded' => 'a&GJcy;b',
            ],
            [
                'decoded' => "a\u0453b",
                'encoded' => 'a&gjcy;b',
            ],
            [
                'decoded' => "a\u2AA5b",
                'encoded' => 'a&gla;b',
            ],
            [
                'decoded' => "a\u2277b",
                'encoded' => 'a&gl;b',
            ],
            [
                'decoded' => "a\u2A92b",
                'encoded' => 'a&glE;b',
            ],
            [
                'decoded' => "a\u2AA4b",
                'encoded' => 'a&glj;b',
            ],
            [
                'decoded' => "a\u2A8Ab",
                'encoded' => 'a&gnap;b',
            ],
            [
                'decoded' => "a\u2A88b",
                'encoded' => 'a&gne;b',
            ],
            [
                'decoded' => "a\u2269b",
                'encoded' => 'a&gnE;b',
            ],
            [
                'decoded' => "a\u22E7b",
                'encoded' => 'a&gnsim;b',
            ],
            [
                'decoded' => "a\uD835\uDD3Eb",
                'encoded' => 'a&Gopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD58b",
                'encoded' => 'a&gopf;b',
            ],
            [
                'decoded' => "a\u2AA2b",
                'encoded' => 'a&GreaterGreater;b',
            ],
            [
                'decoded' => "a\uD835\uDCA2b",
                'encoded' => 'a&Gscr;b',
            ],
            [
                'decoded' => "a\u210Ab",
                'encoded' => 'a&gscr;b',
            ],
            [
                'decoded' => "a\u2273b",
                'encoded' => 'a&gsim;b',
            ],
            [
                'decoded' => "a\u2A8Eb",
                'encoded' => 'a&gsime;b',
            ],
            [
                'decoded' => "a\u2A90b",
                'encoded' => 'a&gsiml;b',
            ],
            [
                'decoded' => "a\u2AA7b",
                'encoded' => 'a&gtcc;b',
            ],
            [
                'decoded' => "a\u2A7Ab",
                'encoded' => 'a&gtcir;b',
            ],
            [
                'decoded' => 'a>b',
                'encoded' => 'a&gt;b',
            ],
            [
                'decoded' => "a\u22D7b",
                'encoded' => 'a&gtdot;b',
            ],
            [
                'decoded' => "a\u2995b",
                'encoded' => 'a&gtlPar;b',
            ],
            [
                'decoded' => "a\u2A7Cb",
                'encoded' => 'a&gtquest;b',
            ],
            [
                'decoded' => "a\u2978b",
                'encoded' => 'a&gtrarr;b',
            ],
            [
                'decoded' => "a\u2269\uFE00b",
                'encoded' => 'a&gvnE;b',
            ],
            [
                'decoded' => "a\u200Ab",
                'encoded' => 'a&hairsp;b',
            ],
            [
                'decoded' => "a\xBDb",
                'encoded' => 'a&half;b',
            ],
            [
                'decoded' => "a\u042Ab",
                'encoded' => 'a&HARDcy;b',
            ],
            [
                'decoded' => "a\u044Ab",
                'encoded' => 'a&hardcy;b',
            ],
            [
                'decoded' => "a\u2948b",
                'encoded' => 'a&harrcir;b',
            ],
            [
                'decoded' => "a\u2194b",
                'encoded' => 'a&harr;b',
            ],
            [
                'decoded' => "a\u21ADb",
                'encoded' => 'a&harrw;b',
            ],
            [
                'decoded' => "a\u210Fb",
                'encoded' => 'a&hbar;b',
            ],
            [
                'decoded' => "a\u0124b",
                'encoded' => 'a&Hcirc;b',
            ],
            [
                'decoded' => "a\u0125b",
                'encoded' => 'a&hcirc;b',
            ],
            [
                'decoded' => "a\u2665b",
                'encoded' => 'a&hearts;b',
            ],
            [
                'decoded' => "a\u22B9b",
                'encoded' => 'a&hercon;b',
            ],
            [
                'decoded' => "a\uD835\uDD25b",
                'encoded' => 'a&hfr;b',
            ],
            [
                'decoded' => "a\u210Cb",
                'encoded' => 'a&Hfr;b',
            ],
            [
                'decoded' => "a\u21FFb",
                'encoded' => 'a&hoarr;b',
            ],
            [
                'decoded' => "a\u223Bb",
                'encoded' => 'a&homtht;b',
            ],
            [
                'decoded' => "a\uD835\uDD59b",
                'encoded' => 'a&hopf;b',
            ],
            [
                'decoded' => "a\u210Db",
                'encoded' => 'a&Hopf;b',
            ],
            [
                'decoded' => "a\u2015b",
                'encoded' => 'a&horbar;b',
            ],
            [
                'decoded' => "a\uD835\uDCBDb",
                'encoded' => 'a&hscr;b',
            ],
            [
                'decoded' => "a\u210Bb",
                'encoded' => 'a&Hscr;b',
            ],
            [
                'decoded' => "a\u0126b",
                'encoded' => 'a&Hstrok;b',
            ],
            [
                'decoded' => "a\u0127b",
                'encoded' => 'a&hstrok;b',
            ],
            [
                'decoded' => "a\u2043b",
                'encoded' => 'a&hybull;b',
            ],
            [
                'decoded' => "a\xCDb",
                'encoded' => 'a&Iacute;b',
            ],
            [
                'decoded' => "a\xEDb",
                'encoded' => 'a&iacute;b',
            ],
            [
                'decoded' => "a\u2063b",
                'encoded' => 'a&ic;b',
            ],
            [
                'decoded' => "a\xCEb",
                'encoded' => 'a&Icirc;b',
            ],
            [
                'decoded' => "a\xEEb",
                'encoded' => 'a&icirc;b',
            ],
            [
                'decoded' => "a\u0418b",
                'encoded' => 'a&Icy;b',
            ],
            [
                'decoded' => "a\u0438b",
                'encoded' => 'a&icy;b',
            ],
            [
                'decoded' => "a\u0130b",
                'encoded' => 'a&Idot;b',
            ],
            [
                'decoded' => "a\u0415b",
                'encoded' => 'a&IEcy;b',
            ],
            [
                'decoded' => "a\u0435b",
                'encoded' => 'a&iecy;b',
            ],
            [
                'decoded' => "a\xA1b",
                'encoded' => 'a&iexcl;b',
            ],
            [
                'decoded' => "a\u21D4b",
                'encoded' => 'a&iff;b',
            ],
            [
                'decoded' => "a\uD835\uDD26b",
                'encoded' => 'a&ifr;b',
            ],
            [
                'decoded' => "a\xCCb",
                'encoded' => 'a&Igrave;b',
            ],
            [
                'decoded' => "a\xECb",
                'encoded' => 'a&igrave;b',
            ],
            [
                'decoded' => "a\u2148b",
                'encoded' => 'a&ii;b',
            ],
            [
                'decoded' => "a\u29DCb",
                'encoded' => 'a&iinfin;b',
            ],
            [
                'decoded' => "a\u2129b",
                'encoded' => 'a&iiota;b',
            ],
            [
                'decoded' => "a\u0132b",
                'encoded' => 'a&IJlig;b',
            ],
            [
                'decoded' => "a\u0133b",
                'encoded' => 'a&ijlig;b',
            ],
            [
                'decoded' => "a\u012Ab",
                'encoded' => 'a&Imacr;b',
            ],
            [
                'decoded' => "a\u012Bb",
                'encoded' => 'a&imacr;b',
            ],
            [
                'decoded' => "a\u0131b",
                'encoded' => 'a&imath;b',
            ],
            [
                'decoded' => "a\u2111b",
                'encoded' => 'a&Im;b',
            ],
            [
                'decoded' => "a\u22B7b",
                'encoded' => 'a&imof;b',
            ],
            [
                'decoded' => "a\u01B5b",
                'encoded' => 'a&imped;b',
            ],
            [
                'decoded' => "a\u2105b",
                'encoded' => 'a&incare;b',
            ],
            [
                'decoded' => "a\u2208b",
                'encoded' => 'a&in;b',
            ],
            [
                'decoded' => "a\u221Eb",
                'encoded' => 'a&infin;b',
            ],
            [
                'decoded' => "a\u29DDb",
                'encoded' => 'a&infintie;b',
            ],
            [
                'decoded' => "a\u22BAb",
                'encoded' => 'a&intcal;b',
            ],
            [
                'decoded' => "a\u222Bb",
                'encoded' => 'a&int;b',
            ],
            [
                'decoded' => "a\u222Cb",
                'encoded' => 'a&Int;b',
            ],
            [
                'decoded' => "a\u2A17b",
                'encoded' => 'a&intlarhk;b',
            ],
            [
                'decoded' => "a\u0401b",
                'encoded' => 'a&IOcy;b',
            ],
            [
                'decoded' => "a\u0451b",
                'encoded' => 'a&iocy;b',
            ],
            [
                'decoded' => "a\u012Eb",
                'encoded' => 'a&Iogon;b',
            ],
            [
                'decoded' => "a\u012Fb",
                'encoded' => 'a&iogon;b',
            ],
            [
                'decoded' => "a\uD835\uDD40b",
                'encoded' => 'a&Iopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD5Ab",
                'encoded' => 'a&iopf;b',
            ],
            [
                'decoded' => "a\u0399b",
                'encoded' => 'a&Iota;b',
            ],
            [
                'decoded' => "a\u03B9b",
                'encoded' => 'a&iota;b',
            ],
            [
                'decoded' => "a\u2A3Cb",
                'encoded' => 'a&iprod;b',
            ],
            [
                'decoded' => "a\xBFb",
                'encoded' => 'a&iquest;b',
            ],
            [
                'decoded' => "a\uD835\uDCBEb",
                'encoded' => 'a&iscr;b',
            ],
            [
                'decoded' => "a\u2110b",
                'encoded' => 'a&Iscr;b',
            ],
            [
                'decoded' => "a\u22F5b",
                'encoded' => 'a&isindot;b',
            ],
            [
                'decoded' => "a\u22F9b",
                'encoded' => 'a&isinE;b',
            ],
            [
                'decoded' => "a\u22F4b",
                'encoded' => 'a&isins;b',
            ],
            [
                'decoded' => "a\u22F3b",
                'encoded' => 'a&isinsv;b',
            ],
            [
                'decoded' => "a\u2062b",
                'encoded' => 'a&it;b',
            ],
            [
                'decoded' => "a\u0128b",
                'encoded' => 'a&Itilde;b',
            ],
            [
                'decoded' => "a\u0129b",
                'encoded' => 'a&itilde;b',
            ],
            [
                'decoded' => "a\u0406b",
                'encoded' => 'a&Iukcy;b',
            ],
            [
                'decoded' => "a\u0456b",
                'encoded' => 'a&iukcy;b',
            ],
            [
                'decoded' => "a\xCFb",
                'encoded' => 'a&Iuml;b',
            ],
            [
                'decoded' => "a\xEFb",
                'encoded' => 'a&iuml;b',
            ],
            [
                'decoded' => "a\u0134b",
                'encoded' => 'a&Jcirc;b',
            ],
            [
                'decoded' => "a\u0135b",
                'encoded' => 'a&jcirc;b',
            ],
            [
                'decoded' => "a\u0419b",
                'encoded' => 'a&Jcy;b',
            ],
            [
                'decoded' => "a\u0439b",
                'encoded' => 'a&jcy;b',
            ],
            [
                'decoded' => "a\uD835\uDD0Db",
                'encoded' => 'a&Jfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD27b",
                'encoded' => 'a&jfr;b',
            ],
            [
                'decoded' => "a\u0237b",
                'encoded' => 'a&jmath;b',
            ],
            [
                'decoded' => "a\uD835\uDD41b",
                'encoded' => 'a&Jopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD5Bb",
                'encoded' => 'a&jopf;b',
            ],
            [
                'decoded' => "a\uD835\uDCA5b",
                'encoded' => 'a&Jscr;b',
            ],
            [
                'decoded' => "a\uD835\uDCBFb",
                'encoded' => 'a&jscr;b',
            ],
            [
                'decoded' => "a\u0408b",
                'encoded' => 'a&Jsercy;b',
            ],
            [
                'decoded' => "a\u0458b",
                'encoded' => 'a&jsercy;b',
            ],
            [
                'decoded' => "a\u0404b",
                'encoded' => 'a&Jukcy;b',
            ],
            [
                'decoded' => "a\u0454b",
                'encoded' => 'a&jukcy;b',
            ],
            [
                'decoded' => "a\u039Ab",
                'encoded' => 'a&Kappa;b',
            ],
            [
                'decoded' => "a\u03BAb",
                'encoded' => 'a&kappa;b',
            ],
            [
                'decoded' => "a\u03F0b",
                'encoded' => 'a&kappav;b',
            ],
            [
                'decoded' => "a\u0136b",
                'encoded' => 'a&Kcedil;b',
            ],
            [
                'decoded' => "a\u0137b",
                'encoded' => 'a&kcedil;b',
            ],
            [
                'decoded' => "a\u041Ab",
                'encoded' => 'a&Kcy;b',
            ],
            [
                'decoded' => "a\u043Ab",
                'encoded' => 'a&kcy;b',
            ],
            [
                'decoded' => "a\uD835\uDD0Eb",
                'encoded' => 'a&Kfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD28b",
                'encoded' => 'a&kfr;b',
            ],
            [
                'decoded' => "a\u0138b",
                'encoded' => 'a&kgreen;b',
            ],
            [
                'decoded' => "a\u0425b",
                'encoded' => 'a&KHcy;b',
            ],
            [
                'decoded' => "a\u0445b",
                'encoded' => 'a&khcy;b',
            ],
            [
                'decoded' => "a\u040Cb",
                'encoded' => 'a&KJcy;b',
            ],
            [
                'decoded' => "a\u045Cb",
                'encoded' => 'a&kjcy;b',
            ],
            [
                'decoded' => "a\uD835\uDD42b",
                'encoded' => 'a&Kopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD5Cb",
                'encoded' => 'a&kopf;b',
            ],
            [
                'decoded' => "a\uD835\uDCA6b",
                'encoded' => 'a&Kscr;b',
            ],
            [
                'decoded' => "a\uD835\uDCC0b",
                'encoded' => 'a&kscr;b',
            ],
            [
                'decoded' => "a\u21DAb",
                'encoded' => 'a&lAarr;b',
            ],
            [
                'decoded' => "a\u0139b",
                'encoded' => 'a&Lacute;b',
            ],
            [
                'decoded' => "a\u013Ab",
                'encoded' => 'a&lacute;b',
            ],
            [
                'decoded' => "a\u29B4b",
                'encoded' => 'a&laemptyv;b',
            ],
            [
                'decoded' => "a\u039Bb",
                'encoded' => 'a&Lambda;b',
            ],
            [
                'decoded' => "a\u03BBb",
                'encoded' => 'a&lambda;b',
            ],
            [
                'decoded' => "a\u27E8b",
                'encoded' => 'a&lang;b',
            ],
            [
                'decoded' => "a\u27EAb",
                'encoded' => 'a&Lang;b',
            ],
            [
                'decoded' => "a\u2991b",
                'encoded' => 'a&langd;b',
            ],
            [
                'decoded' => "a\u2A85b",
                'encoded' => 'a&lap;b',
            ],
            [
                'decoded' => "a\xABb",
                'encoded' => 'a&laquo;b',
            ],
            [
                'decoded' => "a\u21E4b",
                'encoded' => 'a&larrb;b',
            ],
            [
                'decoded' => "a\u291Fb",
                'encoded' => 'a&larrbfs;b',
            ],
            [
                'decoded' => "a\u2190b",
                'encoded' => 'a&larr;b',
            ],
            [
                'decoded' => "a\u219Eb",
                'encoded' => 'a&Larr;b',
            ],
            [
                'decoded' => "a\u21D0b",
                'encoded' => 'a&lArr;b',
            ],
            [
                'decoded' => "a\u291Db",
                'encoded' => 'a&larrfs;b',
            ],
            [
                'decoded' => "a\u21A9b",
                'encoded' => 'a&larrhk;b',
            ],
            [
                'decoded' => "a\u21ABb",
                'encoded' => 'a&larrlp;b',
            ],
            [
                'decoded' => "a\u2939b",
                'encoded' => 'a&larrpl;b',
            ],
            [
                'decoded' => "a\u2973b",
                'encoded' => 'a&larrsim;b',
            ],
            [
                'decoded' => "a\u21A2b",
                'encoded' => 'a&larrtl;b',
            ],
            [
                'decoded' => "a\u2919b",
                'encoded' => 'a&latail;b',
            ],
            [
                'decoded' => "a\u291Bb",
                'encoded' => 'a&lAtail;b',
            ],
            [
                'decoded' => "a\u2AABb",
                'encoded' => 'a&lat;b',
            ],
            [
                'decoded' => "a\u2AADb",
                'encoded' => 'a&late;b',
            ],
            [
                'decoded' => "a\u2AAD\uFE00b",
                'encoded' => 'a&lates;b',
            ],
            [
                'decoded' => "a\u290Cb",
                'encoded' => 'a&lbarr;b',
            ],
            [
                'decoded' => "a\u290Eb",
                'encoded' => 'a&lBarr;b',
            ],
            [
                'decoded' => "a\u2772b",
                'encoded' => 'a&lbbrk;b',
            ],
            [
                'decoded' => "a\u298Bb",
                'encoded' => 'a&lbrke;b',
            ],
            [
                'decoded' => "a\u298Fb",
                'encoded' => 'a&lbrksld;b',
            ],
            [
                'decoded' => "a\u298Db",
                'encoded' => 'a&lbrkslu;b',
            ],
            [
                'decoded' => "a\u013Db",
                'encoded' => 'a&Lcaron;b',
            ],
            [
                'decoded' => "a\u013Eb",
                'encoded' => 'a&lcaron;b',
            ],
            [
                'decoded' => "a\u013Bb",
                'encoded' => 'a&Lcedil;b',
            ],
            [
                'decoded' => "a\u013Cb",
                'encoded' => 'a&lcedil;b',
            ],
            [
                'decoded' => "a\u2308b",
                'encoded' => 'a&lceil;b',
            ],
            [
                'decoded' => "a\u041Bb",
                'encoded' => 'a&Lcy;b',
            ],
            [
                'decoded' => "a\u043Bb",
                'encoded' => 'a&lcy;b',
            ],
            [
                'decoded' => "a\u2936b",
                'encoded' => 'a&ldca;b',
            ],
            [
                'decoded' => "a\u201Cb",
                'encoded' => 'a&ldquo;b',
            ],
            [
                'decoded' => "a\u2967b",
                'encoded' => 'a&ldrdhar;b',
            ],
            [
                'decoded' => "a\u294Bb",
                'encoded' => 'a&ldrushar;b',
            ],
            [
                'decoded' => "a\u21B2b",
                'encoded' => 'a&ldsh;b',
            ],
            [
                'decoded' => "a\u2264b",
                'encoded' => 'a&le;b',
            ],
            [
                'decoded' => "a\u2266b",
                'encoded' => 'a&lE;b',
            ],
            [
                'decoded' => "a\u2961b",
                'encoded' => 'a&LeftDownTeeVector;b',
            ],
            [
                'decoded' => "a\u2959b",
                'encoded' => 'a&LeftDownVectorBar;b',
            ],
            [
                'decoded' => "a\u294Eb",
                'encoded' => 'a&LeftRightVector;b',
            ],
            [
                'decoded' => "a\u295Ab",
                'encoded' => 'a&LeftTeeVector;b',
            ],
            [
                'decoded' => "a\u29CFb",
                'encoded' => 'a&LeftTriangleBar;b',
            ],
            [
                'decoded' => "a\u2951b",
                'encoded' => 'a&LeftUpDownVector;b',
            ],
            [
                'decoded' => "a\u2960b",
                'encoded' => 'a&LeftUpTeeVector;b',
            ],
            [
                'decoded' => "a\u2958b",
                'encoded' => 'a&LeftUpVectorBar;b',
            ],
            [
                'decoded' => "a\u2952b",
                'encoded' => 'a&LeftVectorBar;b',
            ],
            [
                'decoded' => "a\u2A8Bb",
                'encoded' => 'a&lEg;b',
            ],
            [
                'decoded' => "a\u22DAb",
                'encoded' => 'a&leg;b',
            ],
            [
                'decoded' => "a\u2AA8b",
                'encoded' => 'a&lescc;b',
            ],
            [
                'decoded' => "a\u2A7Db",
                'encoded' => 'a&les;b',
            ],
            [
                'decoded' => "a\u2A7Fb",
                'encoded' => 'a&lesdot;b',
            ],
            [
                'decoded' => "a\u2A81b",
                'encoded' => 'a&lesdoto;b',
            ],
            [
                'decoded' => "a\u2A83b",
                'encoded' => 'a&lesdotor;b',
            ],
            [
                'decoded' => "a\u22DA\uFE00b",
                'encoded' => 'a&lesg;b',
            ],
            [
                'decoded' => "a\u2A93b",
                'encoded' => 'a&lesges;b',
            ],
            [
                'decoded' => "a\u2AA1b",
                'encoded' => 'a&LessLess;b',
            ],
            [
                'decoded' => "a\u297Cb",
                'encoded' => 'a&lfisht;b',
            ],
            [
                'decoded' => "a\u230Ab",
                'encoded' => 'a&lfloor;b',
            ],
            [
                'decoded' => "a\uD835\uDD0Fb",
                'encoded' => 'a&Lfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD29b",
                'encoded' => 'a&lfr;b',
            ],
            [
                'decoded' => "a\u2276b",
                'encoded' => 'a&lg;b',
            ],
            [
                'decoded' => "a\u2A91b",
                'encoded' => 'a&lgE;b',
            ],
            [
                'decoded' => "a\u2962b",
                'encoded' => 'a&lHar;b',
            ],
            [
                'decoded' => "a\u21BDb",
                'encoded' => 'a&lhard;b',
            ],
            [
                'decoded' => "a\u21BCb",
                'encoded' => 'a&lharu;b',
            ],
            [
                'decoded' => "a\u296Ab",
                'encoded' => 'a&lharul;b',
            ],
            [
                'decoded' => "a\u2584b",
                'encoded' => 'a&lhblk;b',
            ],
            [
                'decoded' => "a\u0409b",
                'encoded' => 'a&LJcy;b',
            ],
            [
                'decoded' => "a\u0459b",
                'encoded' => 'a&ljcy;b',
            ],
            [
                'decoded' => "a\u21C7b",
                'encoded' => 'a&llarr;b',
            ],
            [
                'decoded' => "a\u226Ab",
                'encoded' => 'a&ll;b',
            ],
            [
                'decoded' => "a\u22D8b",
                'encoded' => 'a&Ll;b',
            ],
            [
                'decoded' => "a\u296Bb",
                'encoded' => 'a&llhard;b',
            ],
            [
                'decoded' => "a\u25FAb",
                'encoded' => 'a&lltri;b',
            ],
            [
                'decoded' => "a\u013Fb",
                'encoded' => 'a&Lmidot;b',
            ],
            [
                'decoded' => "a\u0140b",
                'encoded' => 'a&lmidot;b',
            ],
            [
                'decoded' => "a\u23B0b",
                'encoded' => 'a&lmoust;b',
            ],
            [
                'decoded' => "a\u2A89b",
                'encoded' => 'a&lnap;b',
            ],
            [
                'decoded' => "a\u2A87b",
                'encoded' => 'a&lne;b',
            ],
            [
                'decoded' => "a\u2268b",
                'encoded' => 'a&lnE;b',
            ],
            [
                'decoded' => "a\u22E6b",
                'encoded' => 'a&lnsim;b',
            ],
            [
                'decoded' => "a\u27ECb",
                'encoded' => 'a&loang;b',
            ],
            [
                'decoded' => "a\u21FDb",
                'encoded' => 'a&loarr;b',
            ],
            [
                'decoded' => "a\u27E6b",
                'encoded' => 'a&lobrk;b',
            ],
            [
                'decoded' => "a\u2985b",
                'encoded' => 'a&lopar;b',
            ],
            [
                'decoded' => "a\uD835\uDD43b",
                'encoded' => 'a&Lopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD5Db",
                'encoded' => 'a&lopf;b',
            ],
            [
                'decoded' => "a\u2A2Db",
                'encoded' => 'a&loplus;b',
            ],
            [
                'decoded' => "a\u2A34b",
                'encoded' => 'a&lotimes;b',
            ],
            [
                'decoded' => "a\u2217b",
                'encoded' => 'a&lowast;b',
            ],
            [
                'decoded' => "a\u25CAb",
                'encoded' => 'a&loz;b',
            ],
            [
                'decoded' => "a\u29EBb",
                'encoded' => 'a&lozf;b',
            ],
            [
                'decoded' => "a\u2993b",
                'encoded' => 'a&lparlt;b',
            ],
            [
                'decoded' => "a\u21C6b",
                'encoded' => 'a&lrarr;b',
            ],
            [
                'decoded' => "a\u21CBb",
                'encoded' => 'a&lrhar;b',
            ],
            [
                'decoded' => "a\u296Db",
                'encoded' => 'a&lrhard;b',
            ],
            [
                'decoded' => "a\u200Eb",
                'encoded' => 'a&lrm;b',
            ],
            [
                'decoded' => "a\u22BFb",
                'encoded' => 'a&lrtri;b',
            ],
            [
                'decoded' => "a\u2039b",
                'encoded' => 'a&lsaquo;b',
            ],
            [
                'decoded' => "a\uD835\uDCC1b",
                'encoded' => 'a&lscr;b',
            ],
            [
                'decoded' => "a\u2112b",
                'encoded' => 'a&Lscr;b',
            ],
            [
                'decoded' => "a\u21B0b",
                'encoded' => 'a&lsh;b',
            ],
            [
                'decoded' => "a\u2272b",
                'encoded' => 'a&lsim;b',
            ],
            [
                'decoded' => "a\u2A8Db",
                'encoded' => 'a&lsime;b',
            ],
            [
                'decoded' => "a\u2A8Fb",
                'encoded' => 'a&lsimg;b',
            ],
            [
                'decoded' => "a\u2018b",
                'encoded' => 'a&lsquo;b',
            ],
            [
                'decoded' => "a\u0141b",
                'encoded' => 'a&Lstrok;b',
            ],
            [
                'decoded' => "a\u0142b",
                'encoded' => 'a&lstrok;b',
            ],
            [
                'decoded' => "a\u2AA6b",
                'encoded' => 'a&ltcc;b',
            ],
            [
                'decoded' => "a\u2A79b",
                'encoded' => 'a&ltcir;b',
            ],
            [
                'decoded' => 'a<b',
                'encoded' => 'a&lt;b',
            ],
            [
                'decoded' => "a\u22D6b",
                'encoded' => 'a&ltdot;b',
            ],
            [
                'decoded' => "a\u22CBb",
                'encoded' => 'a&lthree;b',
            ],
            [
                'decoded' => "a\u22C9b",
                'encoded' => 'a&ltimes;b',
            ],
            [
                'decoded' => "a\u2976b",
                'encoded' => 'a&ltlarr;b',
            ],
            [
                'decoded' => "a\u2A7Bb",
                'encoded' => 'a&ltquest;b',
            ],
            [
                'decoded' => "a\u25C3b",
                'encoded' => 'a&ltri;b',
            ],
            [
                'decoded' => "a\u22B4b",
                'encoded' => 'a&ltrie;b',
            ],
            [
                'decoded' => "a\u25C2b",
                'encoded' => 'a&ltrif;b',
            ],
            [
                'decoded' => "a\u2996b",
                'encoded' => 'a&ltrPar;b',
            ],
            [
                'decoded' => "a\u294Ab",
                'encoded' => 'a&lurdshar;b',
            ],
            [
                'decoded' => "a\u2966b",
                'encoded' => 'a&luruhar;b',
            ],
            [
                'decoded' => "a\u2268\uFE00b",
                'encoded' => 'a&lvnE;b',
            ],
            [
                'decoded' => "a\xAFb",
                'encoded' => 'a&macr;b',
            ],
            [
                'decoded' => "a\u2642b",
                'encoded' => 'a&male;b',
            ],
            [
                'decoded' => "a\u2720b",
                'encoded' => 'a&malt;b',
            ],
            [
                'decoded' => "a\u2905b",
                'encoded' => 'a&Map;b',
            ],
            [
                'decoded' => "a\u21A6b",
                'encoded' => 'a&map;b',
            ],
            [
                'decoded' => "a\u21A7b",
                'encoded' => 'a&mapstodown;b',
            ],
            [
                'decoded' => "a\u21A4b",
                'encoded' => 'a&mapstoleft;b',
            ],
            [
                'decoded' => "a\u21A5b",
                'encoded' => 'a&mapstoup;b',
            ],
            [
                'decoded' => "a\u25AEb",
                'encoded' => 'a&marker;b',
            ],
            [
                'decoded' => "a\u2A29b",
                'encoded' => 'a&mcomma;b',
            ],
            [
                'decoded' => "a\u041Cb",
                'encoded' => 'a&Mcy;b',
            ],
            [
                'decoded' => "a\u043Cb",
                'encoded' => 'a&mcy;b',
            ],
            [
                'decoded' => "a\u2014b",
                'encoded' => 'a&mdash;b',
            ],
            [
                'decoded' => "a\u223Ab",
                'encoded' => 'a&mDDot;b',
            ],
            [
                'decoded' => "a\u205Fb",
                'encoded' => 'a&MediumSpace;b',
            ],
            [
                'decoded' => "a\uD835\uDD10b",
                'encoded' => 'a&Mfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD2Ab",
                'encoded' => 'a&mfr;b',
            ],
            [
                'decoded' => "a\u2127b",
                'encoded' => 'a&mho;b',
            ],
            [
                'decoded' => "a\xB5b",
                'encoded' => 'a&micro;b',
            ],
            [
                'decoded' => "a\u2AF0b",
                'encoded' => 'a&midcir;b',
            ],
            [
                'decoded' => "a\u2223b",
                'encoded' => 'a&mid;b',
            ],
            [
                'decoded' => "a\xB7b",
                'encoded' => 'a&middot;b',
            ],
            [
                'decoded' => "a\u229Fb",
                'encoded' => 'a&minusb;b',
            ],
            [
                'decoded' => "a\u2212b",
                'encoded' => 'a&minus;b',
            ],
            [
                'decoded' => "a\u2238b",
                'encoded' => 'a&minusd;b',
            ],
            [
                'decoded' => "a\u2A2Ab",
                'encoded' => 'a&minusdu;b',
            ],
            [
                'decoded' => "a\u2ADBb",
                'encoded' => 'a&mlcp;b',
            ],
            [
                'decoded' => "a\u2026b",
                'encoded' => 'a&mldr;b',
            ],
            [
                'decoded' => "a\u22A7b",
                'encoded' => 'a&models;b',
            ],
            [
                'decoded' => "a\uD835\uDD44b",
                'encoded' => 'a&Mopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD5Eb",
                'encoded' => 'a&mopf;b',
            ],
            [
                'decoded' => "a\u2213b",
                'encoded' => 'a&mp;b',
            ],
            [
                'decoded' => "a\uD835\uDCC2b",
                'encoded' => 'a&mscr;b',
            ],
            [
                'decoded' => "a\u2133b",
                'encoded' => 'a&Mscr;b',
            ],
            [
                'decoded' => "a\u039Cb",
                'encoded' => 'a&Mu;b',
            ],
            [
                'decoded' => "a\u03BCb",
                'encoded' => 'a&mu;b',
            ],
            [
                'decoded' => "a\u22B8b",
                'encoded' => 'a&mumap;b',
            ],
            [
                'decoded' => "a\u0143b",
                'encoded' => 'a&Nacute;b',
            ],
            [
                'decoded' => "a\u0144b",
                'encoded' => 'a&nacute;b',
            ],
            [
                'decoded' => "a\u2220\u20D2b",
                'encoded' => 'a&nang;b',
            ],
            [
                'decoded' => "a\u2249b",
                'encoded' => 'a&nap;b',
            ],
            [
                'decoded' => "a\u2A70\u0338b",
                'encoded' => 'a&napE;b',
            ],
            [
                'decoded' => "a\u224B\u0338b",
                'encoded' => 'a&napid;b',
            ],
            [
                'decoded' => "a\u0149b",
                'encoded' => 'a&napos;b',
            ],
            [
                'decoded' => "a\u266Eb",
                'encoded' => 'a&natur;b',
            ],
            [
                'decoded' => "a\xA0b",
                'encoded' => 'a&nbsp;b',
            ],
            [
                'decoded' => "a\u224E\u0338b",
                'encoded' => 'a&nbump;b',
            ],
            [
                'decoded' => "a\u224F\u0338b",
                'encoded' => 'a&nbumpe;b',
            ],
            [
                'decoded' => "a\u2A43b",
                'encoded' => 'a&ncap;b',
            ],
            [
                'decoded' => "a\u0147b",
                'encoded' => 'a&Ncaron;b',
            ],
            [
                'decoded' => "a\u0148b",
                'encoded' => 'a&ncaron;b',
            ],
            [
                'decoded' => "a\u0145b",
                'encoded' => 'a&Ncedil;b',
            ],
            [
                'decoded' => "a\u0146b",
                'encoded' => 'a&ncedil;b',
            ],
            [
                'decoded' => "a\u2247b",
                'encoded' => 'a&ncong;b',
            ],
            [
                'decoded' => "a\u2A6D\u0338b",
                'encoded' => 'a&ncongdot;b',
            ],
            [
                'decoded' => "a\u2A42b",
                'encoded' => 'a&ncup;b',
            ],
            [
                'decoded' => "a\u041Db",
                'encoded' => 'a&Ncy;b',
            ],
            [
                'decoded' => "a\u043Db",
                'encoded' => 'a&ncy;b',
            ],
            [
                'decoded' => "a\u2013b",
                'encoded' => 'a&ndash;b',
            ],
            [
                'decoded' => "a\u2924b",
                'encoded' => 'a&nearhk;b',
            ],
            [
                'decoded' => "a\u2197b",
                'encoded' => 'a&nearr;b',
            ],
            [
                'decoded' => "a\u21D7b",
                'encoded' => 'a&neArr;b',
            ],
            [
                'decoded' => "a\u2260b",
                'encoded' => 'a&ne;b',
            ],
            [
                'decoded' => "a\u2250\u0338b",
                'encoded' => 'a&nedot;b',
            ],
            [
                'decoded' => "a\u2262b",
                'encoded' => 'a&nequiv;b',
            ],
            [
                'decoded' => "a\u2242\u0338b",
                'encoded' => 'a&nesim;b',
            ],
            [
                'decoded' => "a\nb",
                'encoded' => "a\nb", // `encode` shouldn’t insert `&NewLine;`
            ],
            [
                'decoded' => "a\u2204b",
                'encoded' => 'a&nexist;b',
            ],
            [
                'decoded' => "a\uD835\uDD11b",
                'encoded' => 'a&Nfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD2Bb",
                'encoded' => 'a&nfr;b',
            ],
            [
                'decoded' => "a\u2267\u0338b",
                'encoded' => 'a&ngE;b',
            ],
            [
                'decoded' => "a\u2271b",
                'encoded' => 'a&nge;b',
            ],
            [
                'decoded' => "a\u2A7E\u0338b",
                'encoded' => 'a&nges;b',
            ],
            [
                'decoded' => "a\u22D9\u0338b",
                'encoded' => 'a&nGg;b',
            ],
            [
                'decoded' => "a\u2275b",
                'encoded' => 'a&ngsim;b',
            ],
            [
                'decoded' => "a\u226B\u20D2b",
                'encoded' => 'a&nGt;b',
            ],
            [
                'decoded' => "a\u226Fb",
                'encoded' => 'a&ngt;b',
            ],
            [
                'decoded' => "a\u226B\u0338b",
                'encoded' => 'a&nGtv;b',
            ],
            [
                'decoded' => "a\u21AEb",
                'encoded' => 'a&nharr;b',
            ],
            [
                'decoded' => "a\u21CEb",
                'encoded' => 'a&nhArr;b',
            ],
            [
                'decoded' => "a\u2AF2b",
                'encoded' => 'a&nhpar;b',
            ],
            [
                'decoded' => "a\u220Bb",
                'encoded' => 'a&ni;b',
            ],
            [
                'decoded' => "a\u22FCb",
                'encoded' => 'a&nis;b',
            ],
            [
                'decoded' => "a\u22FAb",
                'encoded' => 'a&nisd;b',
            ],
            [
                'decoded' => "a\u040Ab",
                'encoded' => 'a&NJcy;b',
            ],
            [
                'decoded' => "a\u045Ab",
                'encoded' => 'a&njcy;b',
            ],
            [
                'decoded' => "a\u219Ab",
                'encoded' => 'a&nlarr;b',
            ],
            [
                'decoded' => "a\u21CDb",
                'encoded' => 'a&nlArr;b',
            ],
            [
                'decoded' => "a\u2025b",
                'encoded' => 'a&nldr;b',
            ],
            [
                'decoded' => "a\u2266\u0338b",
                'encoded' => 'a&nlE;b',
            ],
            [
                'decoded' => "a\u2270b",
                'encoded' => 'a&nle;b',
            ],
            [
                'decoded' => "a\u2A7D\u0338b",
                'encoded' => 'a&nles;b',
            ],
            [
                'decoded' => "a\u22D8\u0338b",
                'encoded' => 'a&nLl;b',
            ],
            [
                'decoded' => "a\u2274b",
                'encoded' => 'a&nlsim;b',
            ],
            [
                'decoded' => "a\u226A\u20D2b",
                'encoded' => 'a&nLt;b',
            ],
            [
                'decoded' => "a\u226Eb",
                'encoded' => 'a&nlt;b',
            ],
            [
                'decoded' => "a\u22EAb",
                'encoded' => 'a&nltri;b',
            ],
            [
                'decoded' => "a\u22ECb",
                'encoded' => 'a&nltrie;b',
            ],
            [
                'decoded' => "a\u226A\u0338b",
                'encoded' => 'a&nLtv;b',
            ],
            [
                'decoded' => "a\u2224b",
                'encoded' => 'a&nmid;b',
            ],
            [
                'decoded' => "a\u2060b",
                'encoded' => 'a&NoBreak;b',
            ],
            [
                'decoded' => "a\uD835\uDD5Fb",
                'encoded' => 'a&nopf;b',
            ],
            [
                'decoded' => "a\u2115b",
                'encoded' => 'a&Nopf;b',
            ],
            [
                'decoded' => "a\u2AECb",
                'encoded' => 'a&Not;b',
            ],
            [
                'decoded' => "a\xACb",
                'encoded' => 'a&not;b',
            ],
            [
                'decoded' => "a\u226Db",
                'encoded' => 'a&NotCupCap;b',
            ],
            [
                'decoded' => "a\u2209b",
                'encoded' => 'a&notin;b',
            ],
            [
                'decoded' => "a\u22F5\u0338b",
                'encoded' => 'a&notindot;b',
            ],
            [
                'decoded' => "a\u22F9\u0338b",
                'encoded' => 'a&notinE;b',
            ],
            [
                'decoded' => "a\u22F7b",
                'encoded' => 'a&notinvb;b',
            ],
            [
                'decoded' => "a\u22F6b",
                'encoded' => 'a&notinvc;b',
            ],
            [
                'decoded' => "a\u29CF\u0338b",
                'encoded' => 'a&NotLeftTriangleBar;b',
            ],
            [
                'decoded' => "a\u2AA2\u0338b",
                'encoded' => 'a&NotNestedGreaterGreater;b',
            ],
            [
                'decoded' => "a\u2AA1\u0338b",
                'encoded' => 'a&NotNestedLessLess;b',
            ],
            [
                'decoded' => "a\u220Cb",
                'encoded' => 'a&notni;b',
            ],
            [
                'decoded' => "a\u22FEb",
                'encoded' => 'a&notnivb;b',
            ],
            [
                'decoded' => "a\u22FDb",
                'encoded' => 'a&notnivc;b',
            ],
            [
                'decoded' => "a\u29D0\u0338b",
                'encoded' => 'a&NotRightTriangleBar;b',
            ],
            [
                'decoded' => "a\u228F\u0338b",
                'encoded' => 'a&NotSquareSubset;b',
            ],
            [
                'decoded' => "a\u2290\u0338b",
                'encoded' => 'a&NotSquareSuperset;b',
            ],
            [
                'decoded' => "a\u227F\u0338b",
                'encoded' => 'a&NotSucceedsTilde;b',
            ],
            [
                'decoded' => "a\u2226b",
                'encoded' => 'a&npar;b',
            ],
            [
                'decoded' => "a\u2AFD\u20E5b",
                'encoded' => 'a&nparsl;b',
            ],
            [
                'decoded' => "a\u2202\u0338b",
                'encoded' => 'a&npart;b',
            ],
            [
                'decoded' => "a\u2A14b",
                'encoded' => 'a&npolint;b',
            ],
            [
                'decoded' => "a\u2280b",
                'encoded' => 'a&npr;b',
            ],
            [
                'decoded' => "a\u22E0b",
                'encoded' => 'a&nprcue;b',
            ],
            [
                'decoded' => "a\u2AAF\u0338b",
                'encoded' => 'a&npre;b',
            ],
            [
                'decoded' => "a\u2933\u0338b",
                'encoded' => 'a&nrarrc;b',
            ],
            [
                'decoded' => "a\u219Bb",
                'encoded' => 'a&nrarr;b',
            ],
            [
                'decoded' => "a\u21CFb",
                'encoded' => 'a&nrArr;b',
            ],
            [
                'decoded' => "a\u219D\u0338b",
                'encoded' => 'a&nrarrw;b',
            ],
            [
                'decoded' => "a\u22EBb",
                'encoded' => 'a&nrtri;b',
            ],
            [
                'decoded' => "a\u22EDb",
                'encoded' => 'a&nrtrie;b',
            ],
            [
                'decoded' => "a\u2281b",
                'encoded' => 'a&nsc;b',
            ],
            [
                'decoded' => "a\u22E1b",
                'encoded' => 'a&nsccue;b',
            ],
            [
                'decoded' => "a\u2AB0\u0338b",
                'encoded' => 'a&nsce;b',
            ],
            [
                'decoded' => "a\uD835\uDCA9b",
                'encoded' => 'a&Nscr;b',
            ],
            [
                'decoded' => "a\uD835\uDCC3b",
                'encoded' => 'a&nscr;b',
            ],
            [
                'decoded' => "a\u2241b",
                'encoded' => 'a&nsim;b',
            ],
            [
                'decoded' => "a\u2244b",
                'encoded' => 'a&nsime;b',
            ],
            [
                'decoded' => "a\u22E2b",
                'encoded' => 'a&nsqsube;b',
            ],
            [
                'decoded' => "a\u22E3b",
                'encoded' => 'a&nsqsupe;b',
            ],
            [
                'decoded' => "a\u2284b",
                'encoded' => 'a&nsub;b',
            ],
            [
                'decoded' => "a\u2AC5\u0338b",
                'encoded' => 'a&nsubE;b',
            ],
            [
                'decoded' => "a\u2288b",
                'encoded' => 'a&nsube;b',
            ],
            [
                'decoded' => "a\u2285b",
                'encoded' => 'a&nsup;b',
            ],
            [
                'decoded' => "a\u2AC6\u0338b",
                'encoded' => 'a&nsupE;b',
            ],
            [
                'decoded' => "a\u2289b",
                'encoded' => 'a&nsupe;b',
            ],
            [
                'decoded' => "a\u2279b",
                'encoded' => 'a&ntgl;b',
            ],
            [
                'decoded' => "a\xD1b",
                'encoded' => 'a&Ntilde;b',
            ],
            [
                'decoded' => "a\xF1b",
                'encoded' => 'a&ntilde;b',
            ],
            [
                'decoded' => "a\u2278b",
                'encoded' => 'a&ntlg;b',
            ],
            [
                'decoded' => "a\u039Db",
                'encoded' => 'a&Nu;b',
            ],
            [
                'decoded' => "a\u03BDb",
                'encoded' => 'a&nu;b',
            ],
            [
                'decoded' => "a\u2116b",
                'encoded' => 'a&numero;b',
            ],
            [
                'decoded' => "a\u2007b",
                'encoded' => 'a&numsp;b',
            ],
            [
                'decoded' => "a\u224D\u20D2b",
                'encoded' => 'a&nvap;b',
            ],
            [
                'decoded' => "a\u22ACb",
                'encoded' => 'a&nvdash;b',
            ],
            [
                'decoded' => "a\u22ADb",
                'encoded' => 'a&nvDash;b',
            ],
            [
                'decoded' => "a\u22AEb",
                'encoded' => 'a&nVdash;b',
            ],
            [
                'decoded' => "a\u22AFb",
                'encoded' => 'a&nVDash;b',
            ],
            [
                'decoded' => "a\u2265\u20D2b",
                'encoded' => 'a&nvge;b',
            ],
            [
                'decoded' => "a>\u20D2b",
                'encoded' => 'a&nvgt;b',
            ],
            [
                'decoded' => "a\u2904b",
                'encoded' => 'a&nvHarr;b',
            ],
            [
                'decoded' => "a\u29DEb",
                'encoded' => 'a&nvinfin;b',
            ],
            [
                'decoded' => "a\u2902b",
                'encoded' => 'a&nvlArr;b',
            ],
            [
                'decoded' => "a\u2264\u20D2b",
                'encoded' => 'a&nvle;b',
            ],
            [
                'decoded' => "a<\u20D2b",
                'encoded' => 'a&nvlt;b',
            ],
            [
                'decoded' => "a\u22B4\u20D2b",
                'encoded' => 'a&nvltrie;b',
            ],
            [
                'decoded' => "a\u2903b",
                'encoded' => 'a&nvrArr;b',
            ],
            [
                'decoded' => "a\u22B5\u20D2b",
                'encoded' => 'a&nvrtrie;b',
            ],
            [
                'decoded' => "a\u223C\u20D2b",
                'encoded' => 'a&nvsim;b',
            ],
            [
                'decoded' => "a\u2923b",
                'encoded' => 'a&nwarhk;b',
            ],
            [
                'decoded' => "a\u2196b",
                'encoded' => 'a&nwarr;b',
            ],
            [
                'decoded' => "a\u21D6b",
                'encoded' => 'a&nwArr;b',
            ],
            [
                'decoded' => "a\u2927b",
                'encoded' => 'a&nwnear;b',
            ],
            [
                'decoded' => "a\xD3b",
                'encoded' => 'a&Oacute;b',
            ],
            [
                'decoded' => "a\xF3b",
                'encoded' => 'a&oacute;b',
            ],
            [
                'decoded' => "a\u229Bb",
                'encoded' => 'a&oast;b',
            ],
            [
                'decoded' => "a\xD4b",
                'encoded' => 'a&Ocirc;b',
            ],
            [
                'decoded' => "a\xF4b",
                'encoded' => 'a&ocirc;b',
            ],
            [
                'decoded' => "a\u229Ab",
                'encoded' => 'a&ocir;b',
            ],
            [
                'decoded' => "a\u041Eb",
                'encoded' => 'a&Ocy;b',
            ],
            [
                'decoded' => "a\u043Eb",
                'encoded' => 'a&ocy;b',
            ],
            [
                'decoded' => "a\u229Db",
                'encoded' => 'a&odash;b',
            ],
            [
                'decoded' => "a\u0150b",
                'encoded' => 'a&Odblac;b',
            ],
            [
                'decoded' => "a\u0151b",
                'encoded' => 'a&odblac;b',
            ],
            [
                'decoded' => "a\u2A38b",
                'encoded' => 'a&odiv;b',
            ],
            [
                'decoded' => "a\u2299b",
                'encoded' => 'a&odot;b',
            ],
            [
                'decoded' => "a\u29BCb",
                'encoded' => 'a&odsold;b',
            ],
            [
                'decoded' => "a\u0152b",
                'encoded' => 'a&OElig;b',
            ],
            [
                'decoded' => "a\u0153b",
                'encoded' => 'a&oelig;b',
            ],
            [
                'decoded' => "a\u29BFb",
                'encoded' => 'a&ofcir;b',
            ],
            [
                'decoded' => "a\uD835\uDD12b",
                'encoded' => 'a&Ofr;b',
            ],
            [
                'decoded' => "a\uD835\uDD2Cb",
                'encoded' => 'a&ofr;b',
            ],
            [
                'decoded' => "a\u02DBb",
                'encoded' => 'a&ogon;b',
            ],
            [
                'decoded' => "a\xD2b",
                'encoded' => 'a&Ograve;b',
            ],
            [
                'decoded' => "a\xF2b",
                'encoded' => 'a&ograve;b',
            ],
            [
                'decoded' => "a\u29C1b",
                'encoded' => 'a&ogt;b',
            ],
            [
                'decoded' => "a\u29B5b",
                'encoded' => 'a&ohbar;b',
            ],
            [
                'decoded' => "a\u03A9b",
                'encoded' => 'a&ohm;b',
            ],
            [
                'decoded' => "a\u222Eb",
                'encoded' => 'a&oint;b',
            ],
            [
                'decoded' => "a\u21BAb",
                'encoded' => 'a&olarr;b',
            ],
            [
                'decoded' => "a\u29BEb",
                'encoded' => 'a&olcir;b',
            ],
            [
                'decoded' => "a\u29BBb",
                'encoded' => 'a&olcross;b',
            ],
            [
                'decoded' => "a\u203Eb",
                'encoded' => 'a&oline;b',
            ],
            [
                'decoded' => "a\u29C0b",
                'encoded' => 'a&olt;b',
            ],
            [
                'decoded' => "a\u014Cb",
                'encoded' => 'a&Omacr;b',
            ],
            [
                'decoded' => "a\u014Db",
                'encoded' => 'a&omacr;b',
            ],
            [
                'decoded' => "a\u03C9b",
                'encoded' => 'a&omega;b',
            ],
            [
                'decoded' => "a\u039Fb",
                'encoded' => 'a&Omicron;b',
            ],
            [
                'decoded' => "a\u03BFb",
                'encoded' => 'a&omicron;b',
            ],
            [
                'decoded' => "a\u29B6b",
                'encoded' => 'a&omid;b',
            ],
            [
                'decoded' => "a\u2296b",
                'encoded' => 'a&ominus;b',
            ],
            [
                'decoded' => "a\uD835\uDD46b",
                'encoded' => 'a&Oopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD60b",
                'encoded' => 'a&oopf;b',
            ],
            [
                'decoded' => "a\u29B7b",
                'encoded' => 'a&opar;b',
            ],
            [
                'decoded' => "a\u29B9b",
                'encoded' => 'a&operp;b',
            ],
            [
                'decoded' => "a\u2295b",
                'encoded' => 'a&oplus;b',
            ],
            [
                'decoded' => "a\u21BBb",
                'encoded' => 'a&orarr;b',
            ],
            [
                'decoded' => "a\u2A54b",
                'encoded' => 'a&Or;b',
            ],
            [
                'decoded' => "a\u2228b",
                'encoded' => 'a&or;b',
            ],
            [
                'decoded' => "a\u2A5Db",
                'encoded' => 'a&ord;b',
            ],
            [
                'decoded' => "a\xAAb",
                'encoded' => 'a&ordf;b',
            ],
            [
                'decoded' => "a\xBAb",
                'encoded' => 'a&ordm;b',
            ],
            [
                'decoded' => "a\u22B6b",
                'encoded' => 'a&origof;b',
            ],
            [
                'decoded' => "a\u2A56b",
                'encoded' => 'a&oror;b',
            ],
            [
                'decoded' => "a\u2A57b",
                'encoded' => 'a&orslope;b',
            ],
            [
                'decoded' => "a\u2A5Bb",
                'encoded' => 'a&orv;b',
            ],
            [
                'decoded' => "a\u24C8b",
                'encoded' => 'a&oS;b',
            ],
            [
                'decoded' => "a\uD835\uDCAAb",
                'encoded' => 'a&Oscr;b',
            ],
            [
                'decoded' => "a\u2134b",
                'encoded' => 'a&oscr;b',
            ],
            [
                'decoded' => "a\xD8b",
                'encoded' => 'a&Oslash;b',
            ],
            [
                'decoded' => "a\xF8b",
                'encoded' => 'a&oslash;b',
            ],
            [
                'decoded' => "a\u2298b",
                'encoded' => 'a&osol;b',
            ],
            [
                'decoded' => "a\xD5b",
                'encoded' => 'a&Otilde;b',
            ],
            [
                'decoded' => "a\xF5b",
                'encoded' => 'a&otilde;b',
            ],
            [
                'decoded' => "a\u2A36b",
                'encoded' => 'a&otimesas;b',
            ],
            [
                'decoded' => "a\u2A37b",
                'encoded' => 'a&Otimes;b',
            ],
            [
                'decoded' => "a\u2297b",
                'encoded' => 'a&otimes;b',
            ],
            [
                'decoded' => "a\xD6b",
                'encoded' => 'a&Ouml;b',
            ],
            [
                'decoded' => "a\xF6b",
                'encoded' => 'a&ouml;b',
            ],
            [
                'decoded' => "a\u233Db",
                'encoded' => 'a&ovbar;b',
            ],
            [
                'decoded' => "a\u23DEb",
                'encoded' => 'a&OverBrace;b',
            ],
            [
                'decoded' => "a\u23DCb",
                'encoded' => 'a&OverParenthesis;b',
            ],
            [
                'decoded' => "a\xB6b",
                'encoded' => 'a&para;b',
            ],
            [
                'decoded' => "a\u2225b",
                'encoded' => 'a&par;b',
            ],
            [
                'decoded' => "a\u2AF3b",
                'encoded' => 'a&parsim;b',
            ],
            [
                'decoded' => "a\u2AFDb",
                'encoded' => 'a&parsl;b',
            ],
            [
                'decoded' => "a\u2202b",
                'encoded' => 'a&part;b',
            ],
            [
                'decoded' => "a\u041Fb",
                'encoded' => 'a&Pcy;b',
            ],
            [
                'decoded' => "a\u043Fb",
                'encoded' => 'a&pcy;b',
            ],
            [
                'decoded' => "a\u2030b",
                'encoded' => 'a&permil;b',
            ],
            [
                'decoded' => "a\u2031b",
                'encoded' => 'a&pertenk;b',
            ],
            [
                'decoded' => "a\uD835\uDD13b",
                'encoded' => 'a&Pfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD2Db",
                'encoded' => 'a&pfr;b',
            ],
            [
                'decoded' => "a\u03A6b",
                'encoded' => 'a&Phi;b',
            ],
            [
                'decoded' => "a\u03C6b",
                'encoded' => 'a&phi;b',
            ],
            [
                'decoded' => "a\u03D5b",
                'encoded' => 'a&phiv;b',
            ],
            [
                'decoded' => "a\u260Eb",
                'encoded' => 'a&phone;b',
            ],
            [
                'decoded' => "a\u03A0b",
                'encoded' => 'a&Pi;b',
            ],
            [
                'decoded' => "a\u03C0b",
                'encoded' => 'a&pi;b',
            ],
            [
                'decoded' => "a\u03D6b",
                'encoded' => 'a&piv;b',
            ],
            [
                'decoded' => "a\u210Eb",
                'encoded' => 'a&planckh;b',
            ],
            [
                'decoded' => "a\u2A23b",
                'encoded' => 'a&plusacir;b',
            ],
            [
                'decoded' => "a\u229Eb",
                'encoded' => 'a&plusb;b',
            ],
            [
                'decoded' => "a\u2A22b",
                'encoded' => 'a&pluscir;b',
            ],
            [
                'decoded' => "a\u2214b",
                'encoded' => 'a&plusdo;b',
            ],
            [
                'decoded' => "a\u2A25b",
                'encoded' => 'a&plusdu;b',
            ],
            [
                'decoded' => "a\u2A72b",
                'encoded' => 'a&pluse;b',
            ],
            [
                'decoded' => "a\u2A26b",
                'encoded' => 'a&plussim;b',
            ],
            [
                'decoded' => "a\u2A27b",
                'encoded' => 'a&plustwo;b',
            ],
            [
                'decoded' => "a\xB1b",
                'encoded' => 'a&pm;b',
            ],
            [
                'decoded' => "a\u2A15b",
                'encoded' => 'a&pointint;b',
            ],
            [
                'decoded' => "a\uD835\uDD61b",
                'encoded' => 'a&popf;b',
            ],
            [
                'decoded' => "a\u2119b",
                'encoded' => 'a&Popf;b',
            ],
            [
                'decoded' => "a\xA3b",
                'encoded' => 'a&pound;b',
            ],
            [
                'decoded' => "a\u2AB7b",
                'encoded' => 'a&prap;b',
            ],
            [
                'decoded' => "a\u2ABBb",
                'encoded' => 'a&Pr;b',
            ],
            [
                'decoded' => "a\u227Ab",
                'encoded' => 'a&pr;b',
            ],
            [
                'decoded' => "a\u227Cb",
                'encoded' => 'a&prcue;b',
            ],
            [
                'decoded' => "a\u2AAFb",
                'encoded' => 'a&pre;b',
            ],
            [
                'decoded' => "a\u2AB3b",
                'encoded' => 'a&prE;b',
            ],
            [
                'decoded' => "a\u2032b",
                'encoded' => 'a&prime;b',
            ],
            [
                'decoded' => "a\u2033b",
                'encoded' => 'a&Prime;b',
            ],
            [
                'decoded' => "a\u2AB9b",
                'encoded' => 'a&prnap;b',
            ],
            [
                'decoded' => "a\u2AB5b",
                'encoded' => 'a&prnE;b',
            ],
            [
                'decoded' => "a\u22E8b",
                'encoded' => 'a&prnsim;b',
            ],
            [
                'decoded' => "a\u220Fb",
                'encoded' => 'a&prod;b',
            ],
            [
                'decoded' => "a\u232Eb",
                'encoded' => 'a&profalar;b',
            ],
            [
                'decoded' => "a\u2312b",
                'encoded' => 'a&profline;b',
            ],
            [
                'decoded' => "a\u2313b",
                'encoded' => 'a&profsurf;b',
            ],
            [
                'decoded' => "a\u221Db",
                'encoded' => 'a&prop;b',
            ],
            [
                'decoded' => "a\u227Eb",
                'encoded' => 'a&prsim;b',
            ],
            [
                'decoded' => "a\u22B0b",
                'encoded' => 'a&prurel;b',
            ],
            [
                'decoded' => "a\uD835\uDCABb",
                'encoded' => 'a&Pscr;b',
            ],
            [
                'decoded' => "a\uD835\uDCC5b",
                'encoded' => 'a&pscr;b',
            ],
            [
                'decoded' => "a\u03A8b",
                'encoded' => 'a&Psi;b',
            ],
            [
                'decoded' => "a\u03C8b",
                'encoded' => 'a&psi;b',
            ],
            [
                'decoded' => "a\u2008b",
                'encoded' => 'a&puncsp;b',
            ],
            [
                'decoded' => "a\uD835\uDD14b",
                'encoded' => 'a&Qfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD2Eb",
                'encoded' => 'a&qfr;b',
            ],
            [
                'decoded' => "a\u2A0Cb",
                'encoded' => 'a&qint;b',
            ],
            [
                'decoded' => "a\uD835\uDD62b",
                'encoded' => 'a&qopf;b',
            ],
            [
                'decoded' => "a\u211Ab",
                'encoded' => 'a&Qopf;b',
            ],
            [
                'decoded' => "a\u2057b",
                'encoded' => 'a&qprime;b',
            ],
            [
                'decoded' => "a\uD835\uDCACb",
                'encoded' => 'a&Qscr;b',
            ],
            [
                'decoded' => "a\uD835\uDCC6b",
                'encoded' => 'a&qscr;b',
            ],
            [
                'decoded' => "a\u2A16b",
                'encoded' => 'a&quatint;b',
            ],
            [
                'decoded' => 'a"b',
                'encoded' => 'a&quot;b',
            ],
            [
                'decoded' => "a\u21DBb",
                'encoded' => 'a&rAarr;b',
            ],
            [
                'decoded' => "a\u223D\u0331b",
                'encoded' => 'a&race;b',
            ],
            [
                'decoded' => "a\u0154b",
                'encoded' => 'a&Racute;b',
            ],
            [
                'decoded' => "a\u0155b",
                'encoded' => 'a&racute;b',
            ],
            [
                'decoded' => "a\u29B3b",
                'encoded' => 'a&raemptyv;b',
            ],
            [
                'decoded' => "a\u27E9b",
                'encoded' => 'a&rang;b',
            ],
            [
                'decoded' => "a\u27EBb",
                'encoded' => 'a&Rang;b',
            ],
            [
                'decoded' => "a\u2992b",
                'encoded' => 'a&rangd;b',
            ],
            [
                'decoded' => "a\u29A5b",
                'encoded' => 'a&range;b',
            ],
            [
                'decoded' => "a\xBBb",
                'encoded' => 'a&raquo;b',
            ],
            [
                'decoded' => "a\u2975b",
                'encoded' => 'a&rarrap;b',
            ],
            [
                'decoded' => "a\u21E5b",
                'encoded' => 'a&rarrb;b',
            ],
            [
                'decoded' => "a\u2920b",
                'encoded' => 'a&rarrbfs;b',
            ],
            [
                'decoded' => "a\u2933b",
                'encoded' => 'a&rarrc;b',
            ],
            [
                'decoded' => "a\u2192b",
                'encoded' => 'a&rarr;b',
            ],
            [
                'decoded' => "a\u21A0b",
                'encoded' => 'a&Rarr;b',
            ],
            [
                'decoded' => "a\u21D2b",
                'encoded' => 'a&rArr;b',
            ],
            [
                'decoded' => "a\u291Eb",
                'encoded' => 'a&rarrfs;b',
            ],
            [
                'decoded' => "a\u21AAb",
                'encoded' => 'a&rarrhk;b',
            ],
            [
                'decoded' => "a\u21ACb",
                'encoded' => 'a&rarrlp;b',
            ],
            [
                'decoded' => "a\u2945b",
                'encoded' => 'a&rarrpl;b',
            ],
            [
                'decoded' => "a\u2974b",
                'encoded' => 'a&rarrsim;b',
            ],
            [
                'decoded' => "a\u2916b",
                'encoded' => 'a&Rarrtl;b',
            ],
            [
                'decoded' => "a\u21A3b",
                'encoded' => 'a&rarrtl;b',
            ],
            [
                'decoded' => "a\u219Db",
                'encoded' => 'a&rarrw;b',
            ],
            [
                'decoded' => "a\u291Ab",
                'encoded' => 'a&ratail;b',
            ],
            [
                'decoded' => "a\u291Cb",
                'encoded' => 'a&rAtail;b',
            ],
            [
                'decoded' => "a\u2236b",
                'encoded' => 'a&ratio;b',
            ],
            [
                'decoded' => "a\u290Db",
                'encoded' => 'a&rbarr;b',
            ],
            [
                'decoded' => "a\u290Fb",
                'encoded' => 'a&rBarr;b',
            ],
            [
                'decoded' => "a\u2910b",
                'encoded' => 'a&RBarr;b',
            ],
            [
                'decoded' => "a\u2773b",
                'encoded' => 'a&rbbrk;b',
            ],
            [
                'decoded' => "a\u298Cb",
                'encoded' => 'a&rbrke;b',
            ],
            [
                'decoded' => "a\u298Eb",
                'encoded' => 'a&rbrksld;b',
            ],
            [
                'decoded' => "a\u2990b",
                'encoded' => 'a&rbrkslu;b',
            ],
            [
                'decoded' => "a\u0158b",
                'encoded' => 'a&Rcaron;b',
            ],
            [
                'decoded' => "a\u0159b",
                'encoded' => 'a&rcaron;b',
            ],
            [
                'decoded' => "a\u0156b",
                'encoded' => 'a&Rcedil;b',
            ],
            [
                'decoded' => "a\u0157b",
                'encoded' => 'a&rcedil;b',
            ],
            [
                'decoded' => "a\u2309b",
                'encoded' => 'a&rceil;b',
            ],
            [
                'decoded' => "a\u0420b",
                'encoded' => 'a&Rcy;b',
            ],
            [
                'decoded' => "a\u0440b",
                'encoded' => 'a&rcy;b',
            ],
            [
                'decoded' => "a\u2937b",
                'encoded' => 'a&rdca;b',
            ],
            [
                'decoded' => "a\u2969b",
                'encoded' => 'a&rdldhar;b',
            ],
            [
                'decoded' => "a\u201Db",
                'encoded' => 'a&rdquo;b',
            ],
            [
                'decoded' => "a\u21B3b",
                'encoded' => 'a&rdsh;b',
            ],
            [
                'decoded' => "a\u211Cb",
                'encoded' => 'a&Re;b',
            ],
            [
                'decoded' => "a\u25ADb",
                'encoded' => 'a&rect;b',
            ],
            [
                'decoded' => "a\xAEb",
                'encoded' => 'a&reg;b',
            ],
            [
                'decoded' => "a\u297Db",
                'encoded' => 'a&rfisht;b',
            ],
            [
                'decoded' => "a\u230Bb",
                'encoded' => 'a&rfloor;b',
            ],
            [
                'decoded' => "a\uD835\uDD2Fb",
                'encoded' => 'a&rfr;b',
            ],
            [
                'decoded' => "a\u2964b",
                'encoded' => 'a&rHar;b',
            ],
            [
                'decoded' => "a\u21C1b",
                'encoded' => 'a&rhard;b',
            ],
            [
                'decoded' => "a\u21C0b",
                'encoded' => 'a&rharu;b',
            ],
            [
                'decoded' => "a\u296Cb",
                'encoded' => 'a&rharul;b',
            ],
            [
                'decoded' => "a\u03A1b",
                'encoded' => 'a&Rho;b',
            ],
            [
                'decoded' => "a\u03C1b",
                'encoded' => 'a&rho;b',
            ],
            [
                'decoded' => "a\u03F1b",
                'encoded' => 'a&rhov;b',
            ],
            [
                'decoded' => "a\u295Db",
                'encoded' => 'a&RightDownTeeVector;b',
            ],
            [
                'decoded' => "a\u2955b",
                'encoded' => 'a&RightDownVectorBar;b',
            ],
            [
                'decoded' => "a\u295Bb",
                'encoded' => 'a&RightTeeVector;b',
            ],
            [
                'decoded' => "a\u29D0b",
                'encoded' => 'a&RightTriangleBar;b',
            ],
            [
                'decoded' => "a\u294Fb",
                'encoded' => 'a&RightUpDownVector;b',
            ],
            [
                'decoded' => "a\u295Cb",
                'encoded' => 'a&RightUpTeeVector;b',
            ],
            [
                'decoded' => "a\u2954b",
                'encoded' => 'a&RightUpVectorBar;b',
            ],
            [
                'decoded' => "a\u2953b",
                'encoded' => 'a&RightVectorBar;b',
            ],
            [
                'decoded' => "a\u02DAb",
                'encoded' => 'a&ring;b',
            ],
            [
                'decoded' => "a\u21C4b",
                'encoded' => 'a&rlarr;b',
            ],
            [
                'decoded' => "a\u21CCb",
                'encoded' => 'a&rlhar;b',
            ],
            [
                'decoded' => "a\u200Fb",
                'encoded' => 'a&rlm;b',
            ],
            [
                'decoded' => "a\u23B1b",
                'encoded' => 'a&rmoust;b',
            ],
            [
                'decoded' => "a\u2AEEb",
                'encoded' => 'a&rnmid;b',
            ],
            [
                'decoded' => "a\u27EDb",
                'encoded' => 'a&roang;b',
            ],
            [
                'decoded' => "a\u21FEb",
                'encoded' => 'a&roarr;b',
            ],
            [
                'decoded' => "a\u27E7b",
                'encoded' => 'a&robrk;b',
            ],
            [
                'decoded' => "a\u2986b",
                'encoded' => 'a&ropar;b',
            ],
            [
                'decoded' => "a\uD835\uDD63b",
                'encoded' => 'a&ropf;b',
            ],
            [
                'decoded' => "a\u211Db",
                'encoded' => 'a&Ropf;b',
            ],
            [
                'decoded' => "a\u2A2Eb",
                'encoded' => 'a&roplus;b',
            ],
            [
                'decoded' => "a\u2A35b",
                'encoded' => 'a&rotimes;b',
            ],
            [
                'decoded' => "a\u2970b",
                'encoded' => 'a&RoundImplies;b',
            ],
            [
                'decoded' => "a\u2994b",
                'encoded' => 'a&rpargt;b',
            ],
            [
                'decoded' => "a\u2A12b",
                'encoded' => 'a&rppolint;b',
            ],
            [
                'decoded' => "a\u21C9b",
                'encoded' => 'a&rrarr;b',
            ],
            [
                'decoded' => "a\u203Ab",
                'encoded' => 'a&rsaquo;b',
            ],
            [
                'decoded' => "a\uD835\uDCC7b",
                'encoded' => 'a&rscr;b',
            ],
            [
                'decoded' => "a\u211Bb",
                'encoded' => 'a&Rscr;b',
            ],
            [
                'decoded' => "a\u21B1b",
                'encoded' => 'a&rsh;b',
            ],
            [
                'decoded' => "a\u2019b",
                'encoded' => 'a&rsquo;b',
            ],
            [
                'decoded' => "a\u22CCb",
                'encoded' => 'a&rthree;b',
            ],
            [
                'decoded' => "a\u22CAb",
                'encoded' => 'a&rtimes;b',
            ],
            [
                'decoded' => "a\u25B9b",
                'encoded' => 'a&rtri;b',
            ],
            [
                'decoded' => "a\u22B5b",
                'encoded' => 'a&rtrie;b',
            ],
            [
                'decoded' => "a\u25B8b",
                'encoded' => 'a&rtrif;b',
            ],
            [
                'decoded' => "a\u29CEb",
                'encoded' => 'a&rtriltri;b',
            ],
            [
                'decoded' => "a\u29F4b",
                'encoded' => 'a&RuleDelayed;b',
            ],
            [
                'decoded' => "a\u2968b",
                'encoded' => 'a&ruluhar;b',
            ],
            [
                'decoded' => "a\u211Eb",
                'encoded' => 'a&rx;b',
            ],
            [
                'decoded' => "a\u015Ab",
                'encoded' => 'a&Sacute;b',
            ],
            [
                'decoded' => "a\u015Bb",
                'encoded' => 'a&sacute;b',
            ],
            [
                'decoded' => "a\u201Ab",
                'encoded' => 'a&sbquo;b',
            ],
            [
                'decoded' => "a\u2AB8b",
                'encoded' => 'a&scap;b',
            ],
            [
                'decoded' => "a\u0160b",
                'encoded' => 'a&Scaron;b',
            ],
            [
                'decoded' => "a\u0161b",
                'encoded' => 'a&scaron;b',
            ],
            [
                'decoded' => "a\u2ABCb",
                'encoded' => 'a&Sc;b',
            ],
            [
                'decoded' => "a\u227Bb",
                'encoded' => 'a&sc;b',
            ],
            [
                'decoded' => "a\u227Db",
                'encoded' => 'a&sccue;b',
            ],
            [
                'decoded' => "a\u2AB0b",
                'encoded' => 'a&sce;b',
            ],
            [
                'decoded' => "a\u2AB4b",
                'encoded' => 'a&scE;b',
            ],
            [
                'decoded' => "a\u015Eb",
                'encoded' => 'a&Scedil;b',
            ],
            [
                'decoded' => "a\u015Fb",
                'encoded' => 'a&scedil;b',
            ],
            [
                'decoded' => "a\u015Cb",
                'encoded' => 'a&Scirc;b',
            ],
            [
                'decoded' => "a\u015Db",
                'encoded' => 'a&scirc;b',
            ],
            [
                'decoded' => "a\u2ABAb",
                'encoded' => 'a&scnap;b',
            ],
            [
                'decoded' => "a\u2AB6b",
                'encoded' => 'a&scnE;b',
            ],
            [
                'decoded' => "a\u22E9b",
                'encoded' => 'a&scnsim;b',
            ],
            [
                'decoded' => "a\u2A13b",
                'encoded' => 'a&scpolint;b',
            ],
            [
                'decoded' => "a\u227Fb",
                'encoded' => 'a&scsim;b',
            ],
            [
                'decoded' => "a\u0421b",
                'encoded' => 'a&Scy;b',
            ],
            [
                'decoded' => "a\u0441b",
                'encoded' => 'a&scy;b',
            ],
            [
                'decoded' => "a\u22A1b",
                'encoded' => 'a&sdotb;b',
            ],
            [
                'decoded' => "a\u22C5b",
                'encoded' => 'a&sdot;b',
            ],
            [
                'decoded' => "a\u2A66b",
                'encoded' => 'a&sdote;b',
            ],
            [
                'decoded' => "a\u2925b",
                'encoded' => 'a&searhk;b',
            ],
            [
                'decoded' => "a\u2198b",
                'encoded' => 'a&searr;b',
            ],
            [
                'decoded' => "a\u21D8b",
                'encoded' => 'a&seArr;b',
            ],
            [
                'decoded' => "a\xA7b",
                'encoded' => 'a&sect;b',
            ],
            [
                'decoded' => "a\u2216b",
                'encoded' => 'a&setmn;b',
            ],
            [
                'decoded' => "a\u2736b",
                'encoded' => 'a&sext;b',
            ],
            [
                'decoded' => "a\uD835\uDD16b",
                'encoded' => 'a&Sfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD30b",
                'encoded' => 'a&sfr;b',
            ],
            [
                'decoded' => "a\u266Fb",
                'encoded' => 'a&sharp;b',
            ],
            [
                'decoded' => "a\u0429b",
                'encoded' => 'a&SHCHcy;b',
            ],
            [
                'decoded' => "a\u0449b",
                'encoded' => 'a&shchcy;b',
            ],
            [
                'decoded' => "a\u0428b",
                'encoded' => 'a&SHcy;b',
            ],
            [
                'decoded' => "a\u0448b",
                'encoded' => 'a&shcy;b',
            ],
            [
                'decoded' => "a\xADb",
                'encoded' => 'a&shy;b',
            ],
            [
                'decoded' => "a\u03A3b",
                'encoded' => 'a&Sigma;b',
            ],
            [
                'decoded' => "a\u03C3b",
                'encoded' => 'a&sigma;b',
            ],
            [
                'decoded' => "a\u03C2b",
                'encoded' => 'a&sigmaf;b',
            ],
            [
                'decoded' => "a\u223Cb",
                'encoded' => 'a&sim;b',
            ],
            [
                'decoded' => "a\u2A6Ab",
                'encoded' => 'a&simdot;b',
            ],
            [
                'decoded' => "a\u2243b",
                'encoded' => 'a&sime;b',
            ],
            [
                'decoded' => "a\u2A9Eb",
                'encoded' => 'a&simg;b',
            ],
            [
                'decoded' => "a\u2AA0b",
                'encoded' => 'a&simgE;b',
            ],
            [
                'decoded' => "a\u2A9Db",
                'encoded' => 'a&siml;b',
            ],
            [
                'decoded' => "a\u2A9Fb",
                'encoded' => 'a&simlE;b',
            ],
            [
                'decoded' => "a\u2246b",
                'encoded' => 'a&simne;b',
            ],
            [
                'decoded' => "a\u2A24b",
                'encoded' => 'a&simplus;b',
            ],
            [
                'decoded' => "a\u2972b",
                'encoded' => 'a&simrarr;b',
            ],
            [
                'decoded' => "a\u2A33b",
                'encoded' => 'a&smashp;b',
            ],
            [
                'decoded' => "a\u29E4b",
                'encoded' => 'a&smeparsl;b',
            ],
            [
                'decoded' => "a\u2323b",
                'encoded' => 'a&smile;b',
            ],
            [
                'decoded' => "a\u2AAAb",
                'encoded' => 'a&smt;b',
            ],
            [
                'decoded' => "a\u2AACb",
                'encoded' => 'a&smte;b',
            ],
            [
                'decoded' => "a\u2AAC\uFE00b",
                'encoded' => 'a&smtes;b',
            ],
            [
                'decoded' => "a\u042Cb",
                'encoded' => 'a&SOFTcy;b',
            ],
            [
                'decoded' => "a\u044Cb",
                'encoded' => 'a&softcy;b',
            ],
            [
                'decoded' => "a\u233Fb",
                'encoded' => 'a&solbar;b',
            ],
            [
                'decoded' => "a\u29C4b",
                'encoded' => 'a&solb;b',
            ],
            [
                'decoded' => "a\uD835\uDD4Ab",
                'encoded' => 'a&Sopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD64b",
                'encoded' => 'a&sopf;b',
            ],
            [
                'decoded' => "a\u2660b",
                'encoded' => 'a&spades;b',
            ],
            [
                'decoded' => "a\u2293b",
                'encoded' => 'a&sqcap;b',
            ],
            [
                'decoded' => "a\u2293\uFE00b",
                'encoded' => 'a&sqcaps;b',
            ],
            [
                'decoded' => "a\u2294b",
                'encoded' => 'a&sqcup;b',
            ],
            [
                'decoded' => "a\u2294\uFE00b",
                'encoded' => 'a&sqcups;b',
            ],
            [
                'decoded' => "a\u221Ab",
                'encoded' => 'a&Sqrt;b',
            ],
            [
                'decoded' => "a\u228Fb",
                'encoded' => 'a&sqsub;b',
            ],
            [
                'decoded' => "a\u2291b",
                'encoded' => 'a&sqsube;b',
            ],
            [
                'decoded' => "a\u2290b",
                'encoded' => 'a&sqsup;b',
            ],
            [
                'decoded' => "a\u2292b",
                'encoded' => 'a&sqsupe;b',
            ],
            [
                'decoded' => "a\u25A1b",
                'encoded' => 'a&squ;b',
            ],
            [
                'decoded' => "a\u25AAb",
                'encoded' => 'a&squf;b',
            ],
            [
                'decoded' => "a\uD835\uDCAEb",
                'encoded' => 'a&Sscr;b',
            ],
            [
                'decoded' => "a\uD835\uDCC8b",
                'encoded' => 'a&sscr;b',
            ],
            [
                'decoded' => "a\u22C6b",
                'encoded' => 'a&Star;b',
            ],
            [
                'decoded' => "a\u2606b",
                'encoded' => 'a&star;b',
            ],
            [
                'decoded' => "a\u2605b",
                'encoded' => 'a&starf;b',
            ],
            [
                'decoded' => "a\u2282b",
                'encoded' => 'a&sub;b',
            ],
            [
                'decoded' => "a\u22D0b",
                'encoded' => 'a&Sub;b',
            ],
            [
                'decoded' => "a\u2ABDb",
                'encoded' => 'a&subdot;b',
            ],
            [
                'decoded' => "a\u2AC5b",
                'encoded' => 'a&subE;b',
            ],
            [
                'decoded' => "a\u2286b",
                'encoded' => 'a&sube;b',
            ],
            [
                'decoded' => "a\u2AC3b",
                'encoded' => 'a&subedot;b',
            ],
            [
                'decoded' => "a\u2AC1b",
                'encoded' => 'a&submult;b',
            ],
            [
                'decoded' => "a\u2ACBb",
                'encoded' => 'a&subnE;b',
            ],
            [
                'decoded' => "a\u228Ab",
                'encoded' => 'a&subne;b',
            ],
            [
                'decoded' => "a\u2ABFb",
                'encoded' => 'a&subplus;b',
            ],
            [
                'decoded' => "a\u2979b",
                'encoded' => 'a&subrarr;b',
            ],
            [
                'decoded' => "a\u2AC7b",
                'encoded' => 'a&subsim;b',
            ],
            [
                'decoded' => "a\u2AD5b",
                'encoded' => 'a&subsub;b',
            ],
            [
                'decoded' => "a\u2AD3b",
                'encoded' => 'a&subsup;b',
            ],
            [
                'decoded' => "a\u2211b",
                'encoded' => 'a&sum;b',
            ],
            [
                'decoded' => "a\u266Ab",
                'encoded' => 'a&sung;b',
            ],
            [
                'decoded' => "a\xB9b",
                'encoded' => 'a&sup1;b',
            ],
            [
                'decoded' => "a\xB2b",
                'encoded' => 'a&sup2;b',
            ],
            [
                'decoded' => "a\xB3b",
                'encoded' => 'a&sup3;b',
            ],
            [
                'decoded' => "a\u2283b",
                'encoded' => 'a&sup;b',
            ],
            [
                'decoded' => "a\u22D1b",
                'encoded' => 'a&Sup;b',
            ],
            [
                'decoded' => "a\u2ABEb",
                'encoded' => 'a&supdot;b',
            ],
            [
                'decoded' => "a\u2AD8b",
                'encoded' => 'a&supdsub;b',
            ],
            [
                'decoded' => "a\u2AC6b",
                'encoded' => 'a&supE;b',
            ],
            [
                'decoded' => "a\u2287b",
                'encoded' => 'a&supe;b',
            ],
            [
                'decoded' => "a\u2AC4b",
                'encoded' => 'a&supedot;b',
            ],
            [
                'decoded' => "a\u27C9b",
                'encoded' => 'a&suphsol;b',
            ],
            [
                'decoded' => "a\u2AD7b",
                'encoded' => 'a&suphsub;b',
            ],
            [
                'decoded' => "a\u297Bb",
                'encoded' => 'a&suplarr;b',
            ],
            [
                'decoded' => "a\u2AC2b",
                'encoded' => 'a&supmult;b',
            ],
            [
                'decoded' => "a\u2ACCb",
                'encoded' => 'a&supnE;b',
            ],
            [
                'decoded' => "a\u228Bb",
                'encoded' => 'a&supne;b',
            ],
            [
                'decoded' => "a\u2AC0b",
                'encoded' => 'a&supplus;b',
            ],
            [
                'decoded' => "a\u2AC8b",
                'encoded' => 'a&supsim;b',
            ],
            [
                'decoded' => "a\u2AD4b",
                'encoded' => 'a&supsub;b',
            ],
            [
                'decoded' => "a\u2AD6b",
                'encoded' => 'a&supsup;b',
            ],
            [
                'decoded' => "a\u2926b",
                'encoded' => 'a&swarhk;b',
            ],
            [
                'decoded' => "a\u2199b",
                'encoded' => 'a&swarr;b',
            ],
            [
                'decoded' => "a\u21D9b",
                'encoded' => 'a&swArr;b',
            ],
            [
                'decoded' => "a\u292Ab",
                'encoded' => 'a&swnwar;b',
            ],
            [
                'decoded' => "a\xDFb",
                'encoded' => 'a&szlig;b',
            ],
            [
                'decoded' => "a\u2316b",
                'encoded' => 'a&target;b',
            ],
            [
                'decoded' => "a\u03A4b",
                'encoded' => 'a&Tau;b',
            ],
            [
                'decoded' => "a\u03C4b",
                'encoded' => 'a&tau;b',
            ],
            [
                'decoded' => "a\u23B4b",
                'encoded' => 'a&tbrk;b',
            ],
            [
                'decoded' => "a\u0164b",
                'encoded' => 'a&Tcaron;b',
            ],
            [
                'decoded' => "a\u0165b",
                'encoded' => 'a&tcaron;b',
            ],
            [
                'decoded' => "a\u0162b",
                'encoded' => 'a&Tcedil;b',
            ],
            [
                'decoded' => "a\u0163b",
                'encoded' => 'a&tcedil;b',
            ],
            [
                'decoded' => "a\u0422b",
                'encoded' => 'a&Tcy;b',
            ],
            [
                'decoded' => "a\u0442b",
                'encoded' => 'a&tcy;b',
            ],
            [
                'decoded' => "a\u20DBb",
                'encoded' => 'a&tdot;b',
            ],
            [
                'decoded' => "a\u2315b",
                'encoded' => 'a&telrec;b',
            ],
            [
                'decoded' => "a\uD835\uDD17b",
                'encoded' => 'a&Tfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD31b",
                'encoded' => 'a&tfr;b',
            ],
            [
                'decoded' => "a\u2234b",
                'encoded' => 'a&there4;b',
            ],
            [
                'decoded' => "a\u0398b",
                'encoded' => 'a&Theta;b',
            ],
            [
                'decoded' => "a\u03B8b",
                'encoded' => 'a&theta;b',
            ],
            [
                'decoded' => "a\u03D1b",
                'encoded' => 'a&thetav;b',
            ],
            [
                'decoded' => "a\u205F\u200Ab",
                'encoded' => 'a&ThickSpace;b',
            ],
            [
                'decoded' => "a\u2009b",
                'encoded' => 'a&thinsp;b',
            ],
            [
                'decoded' => "a\xDEb",
                'encoded' => 'a&THORN;b',
            ],
            [
                'decoded' => "a\xFEb",
                'encoded' => 'a&thorn;b',
            ],
            [
                'decoded' => "a\u02DCb",
                'encoded' => 'a&tilde;b',
            ],
            [
                'decoded' => "a\u2A31b",
                'encoded' => 'a&timesbar;b',
            ],
            [
                'decoded' => "a\u22A0b",
                'encoded' => 'a&timesb;b',
            ],
            [
                'decoded' => "a\xD7b",
                'encoded' => 'a&times;b',
            ],
            [
                'decoded' => "a\u2A30b",
                'encoded' => 'a&timesd;b',
            ],
            [
                'decoded' => "a\u222Db",
                'encoded' => 'a&tint;b',
            ],
            [
                'decoded' => "a\u2928b",
                'encoded' => 'a&toea;b',
            ],
            [
                'decoded' => "a\u2336b",
                'encoded' => 'a&topbot;b',
            ],
            [
                'decoded' => "a\u2AF1b",
                'encoded' => 'a&topcir;b',
            ],
            [
                'decoded' => "a\u22A4b",
                'encoded' => 'a&top;b',
            ],
            [
                'decoded' => "a\uD835\uDD4Bb",
                'encoded' => 'a&Topf;b',
            ],
            [
                'decoded' => "a\uD835\uDD65b",
                'encoded' => 'a&topf;b',
            ],
            [
                'decoded' => "a\u2ADAb",
                'encoded' => 'a&topfork;b',
            ],
            [
                'decoded' => "a\u2929b",
                'encoded' => 'a&tosa;b',
            ],
            [
                'decoded' => "a\u2034b",
                'encoded' => 'a&tprime;b',
            ],
            [
                'decoded' => "a\u2122b",
                'encoded' => 'a&trade;b',
            ],
            [
                'decoded' => "a\u25ECb",
                'encoded' => 'a&tridot;b',
            ],
            [
                'decoded' => "a\u225Cb",
                'encoded' => 'a&trie;b',
            ],
            [
                'decoded' => "a\u2A3Ab",
                'encoded' => 'a&triminus;b',
            ],
            [
                'decoded' => "a\u2A39b",
                'encoded' => 'a&triplus;b',
            ],
            [
                'decoded' => "a\u29CDb",
                'encoded' => 'a&trisb;b',
            ],
            [
                'decoded' => "a\u2A3Bb",
                'encoded' => 'a&tritime;b',
            ],
            [
                'decoded' => "a\u23E2b",
                'encoded' => 'a&trpezium;b',
            ],
            [
                'decoded' => "a\uD835\uDCAFb",
                'encoded' => 'a&Tscr;b',
            ],
            [
                'decoded' => "a\uD835\uDCC9b",
                'encoded' => 'a&tscr;b',
            ],
            [
                'decoded' => "a\u0426b",
                'encoded' => 'a&TScy;b',
            ],
            [
                'decoded' => "a\u0446b",
                'encoded' => 'a&tscy;b',
            ],
            [
                'decoded' => "a\u040Bb",
                'encoded' => 'a&TSHcy;b',
            ],
            [
                'decoded' => "a\u045Bb",
                'encoded' => 'a&tshcy;b',
            ],
            [
                'decoded' => "a\u0166b",
                'encoded' => 'a&Tstrok;b',
            ],
            [
                'decoded' => "a\u0167b",
                'encoded' => 'a&tstrok;b',
            ],
            [
                'decoded' => "a\u226Cb",
                'encoded' => 'a&twixt;b',
            ],
            [
                'decoded' => "a\xDAb",
                'encoded' => 'a&Uacute;b',
            ],
            [
                'decoded' => "a\xFAb",
                'encoded' => 'a&uacute;b',
            ],
            [
                'decoded' => "a\u2191b",
                'encoded' => 'a&uarr;b',
            ],
            [
                'decoded' => "a\u219Fb",
                'encoded' => 'a&Uarr;b',
            ],
            [
                'decoded' => "a\u21D1b",
                'encoded' => 'a&uArr;b',
            ],
            [
                'decoded' => "a\u2949b",
                'encoded' => 'a&Uarrocir;b',
            ],
            [
                'decoded' => "a\u040Eb",
                'encoded' => 'a&Ubrcy;b',
            ],
            [
                'decoded' => "a\u045Eb",
                'encoded' => 'a&ubrcy;b',
            ],
            [
                'decoded' => "a\u016Cb",
                'encoded' => 'a&Ubreve;b',
            ],
            [
                'decoded' => "a\u016Db",
                'encoded' => 'a&ubreve;b',
            ],
            [
                'decoded' => "a\xDBb",
                'encoded' => 'a&Ucirc;b',
            ],
            [
                'decoded' => "a\xFBb",
                'encoded' => 'a&ucirc;b',
            ],
            [
                'decoded' => "a\u0423b",
                'encoded' => 'a&Ucy;b',
            ],
            [
                'decoded' => "a\u0443b",
                'encoded' => 'a&ucy;b',
            ],
            [
                'decoded' => "a\u21C5b",
                'encoded' => 'a&udarr;b',
            ],
            [
                'decoded' => "a\u0170b",
                'encoded' => 'a&Udblac;b',
            ],
            [
                'decoded' => "a\u0171b",
                'encoded' => 'a&udblac;b',
            ],
            [
                'decoded' => "a\u296Eb",
                'encoded' => 'a&udhar;b',
            ],
            [
                'decoded' => "a\u297Eb",
                'encoded' => 'a&ufisht;b',
            ],
            [
                'decoded' => "a\uD835\uDD18b",
                'encoded' => 'a&Ufr;b',
            ],
            [
                'decoded' => "a\uD835\uDD32b",
                'encoded' => 'a&ufr;b',
            ],
            [
                'decoded' => "a\xD9b",
                'encoded' => 'a&Ugrave;b',
            ],
            [
                'decoded' => "a\xF9b",
                'encoded' => 'a&ugrave;b',
            ],
            [
                'decoded' => "a\u2963b",
                'encoded' => 'a&uHar;b',
            ],
            [
                'decoded' => "a\u21BFb",
                'encoded' => 'a&uharl;b',
            ],
            [
                'decoded' => "a\u21BEb",
                'encoded' => 'a&uharr;b',
            ],
            [
                'decoded' => "a\u2580b",
                'encoded' => 'a&uhblk;b',
            ],
            [
                'decoded' => "a\u231Cb",
                'encoded' => 'a&ulcorn;b',
            ],
            [
                'decoded' => "a\u230Fb",
                'encoded' => 'a&ulcrop;b',
            ],
            [
                'decoded' => "a\u25F8b",
                'encoded' => 'a&ultri;b',
            ],
            [
                'decoded' => "a\u016Ab",
                'encoded' => 'a&Umacr;b',
            ],
            [
                'decoded' => "a\u016Bb",
                'encoded' => 'a&umacr;b',
            ],
            [
                'decoded' => "a\u23DFb",
                'encoded' => 'a&UnderBrace;b',
            ],
            [
                'decoded' => "a\u23DDb",
                'encoded' => 'a&UnderParenthesis;b',
            ],
            [
                'decoded' => "a\u0172b",
                'encoded' => 'a&Uogon;b',
            ],
            [
                'decoded' => "a\u0173b",
                'encoded' => 'a&uogon;b',
            ],
            [
                'decoded' => "a\uD835\uDD4Cb",
                'encoded' => 'a&Uopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD66b",
                'encoded' => 'a&uopf;b',
            ],
            [
                'decoded' => "a\u2912b",
                'encoded' => 'a&UpArrowBar;b',
            ],
            [
                'decoded' => "a\u228Eb",
                'encoded' => 'a&uplus;b',
            ],
            [
                'decoded' => "a\u03C5b",
                'encoded' => 'a&upsi;b',
            ],
            [
                'decoded' => "a\u03D2b",
                'encoded' => 'a&Upsi;b',
            ],
            [
                'decoded' => "a\u03A5b",
                'encoded' => 'a&Upsilon;b',
            ],
            [
                'decoded' => "a\u231Db",
                'encoded' => 'a&urcorn;b',
            ],
            [
                'decoded' => "a\u230Eb",
                'encoded' => 'a&urcrop;b',
            ],
            [
                'decoded' => "a\u016Eb",
                'encoded' => 'a&Uring;b',
            ],
            [
                'decoded' => "a\u016Fb",
                'encoded' => 'a&uring;b',
            ],
            [
                'decoded' => "a\u25F9b",
                'encoded' => 'a&urtri;b',
            ],
            [
                'decoded' => "a\uD835\uDCB0b",
                'encoded' => 'a&Uscr;b',
            ],
            [
                'decoded' => "a\uD835\uDCCAb",
                'encoded' => 'a&uscr;b',
            ],
            [
                'decoded' => "a\u22F0b",
                'encoded' => 'a&utdot;b',
            ],
            [
                'decoded' => "a\u0168b",
                'encoded' => 'a&Utilde;b',
            ],
            [
                'decoded' => "a\u0169b",
                'encoded' => 'a&utilde;b',
            ],
            [
                'decoded' => "a\u25B5b",
                'encoded' => 'a&utri;b',
            ],
            [
                'decoded' => "a\u25B4b",
                'encoded' => 'a&utrif;b',
            ],
            [
                'decoded' => "a\u21C8b",
                'encoded' => 'a&uuarr;b',
            ],
            [
                'decoded' => "a\xDCb",
                'encoded' => 'a&Uuml;b',
            ],
            [
                'decoded' => "a\xFCb",
                'encoded' => 'a&uuml;b',
            ],
            [
                'decoded' => "a\u29A7b",
                'encoded' => 'a&uwangle;b',
            ],
            [
                'decoded' => "a\u299Cb",
                'encoded' => 'a&vangrt;b',
            ],
            [
                'decoded' => "a\u2195b",
                'encoded' => 'a&varr;b',
            ],
            [
                'decoded' => "a\u21D5b",
                'encoded' => 'a&vArr;b',
            ],
            [
                'decoded' => "a\u2AE8b",
                'encoded' => 'a&vBar;b',
            ],
            [
                'decoded' => "a\u2AEBb",
                'encoded' => 'a&Vbar;b',
            ],
            [
                'decoded' => "a\u2AE9b",
                'encoded' => 'a&vBarv;b',
            ],
            [
                'decoded' => "a\u0412b",
                'encoded' => 'a&Vcy;b',
            ],
            [
                'decoded' => "a\u0432b",
                'encoded' => 'a&vcy;b',
            ],
            [
                'decoded' => "a\u22A2b",
                'encoded' => 'a&vdash;b',
            ],
            [
                'decoded' => "a\u22A8b",
                'encoded' => 'a&vDash;b',
            ],
            [
                'decoded' => "a\u22A9b",
                'encoded' => 'a&Vdash;b',
            ],
            [
                'decoded' => "a\u22ABb",
                'encoded' => 'a&VDash;b',
            ],
            [
                'decoded' => "a\u2AE6b",
                'encoded' => 'a&Vdashl;b',
            ],
            [
                'decoded' => "a\u22BBb",
                'encoded' => 'a&veebar;b',
            ],
            [
                'decoded' => "a\u22C1b",
                'encoded' => 'a&Vee;b',
            ],
            [
                'decoded' => "a\u225Ab",
                'encoded' => 'a&veeeq;b',
            ],
            [
                'decoded' => "a\u22EEb",
                'encoded' => 'a&vellip;b',
            ],
            [
                'decoded' => "a\u2016b",
                'encoded' => 'a&Vert;b',
            ],
            [
                'decoded' => "a\u2758b",
                'encoded' => 'a&VerticalSeparator;b',
            ],
            [
                'decoded' => "a\uD835\uDD19b",
                'encoded' => 'a&Vfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD33b",
                'encoded' => 'a&vfr;b',
            ],
            [
                'decoded' => "a\u22B2b",
                'encoded' => 'a&vltri;b',
            ],
            [
                'decoded' => "a\u2282\u20D2b",
                'encoded' => 'a&vnsub;b',
            ],
            [
                'decoded' => "a\u2283\u20D2b",
                'encoded' => 'a&vnsup;b',
            ],
            [
                'decoded' => "a\uD835\uDD4Db",
                'encoded' => 'a&Vopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD67b",
                'encoded' => 'a&vopf;b',
            ],
            [
                'decoded' => "a\u22B3b",
                'encoded' => 'a&vrtri;b',
            ],
            [
                'decoded' => "a\uD835\uDCB1b",
                'encoded' => 'a&Vscr;b',
            ],
            [
                'decoded' => "a\uD835\uDCCBb",
                'encoded' => 'a&vscr;b',
            ],
            [
                'decoded' => "a\u2ACB\uFE00b",
                'encoded' => 'a&vsubnE;b',
            ],
            [
                'decoded' => "a\u228A\uFE00b",
                'encoded' => 'a&vsubne;b',
            ],
            [
                'decoded' => "a\u2ACC\uFE00b",
                'encoded' => 'a&vsupnE;b',
            ],
            [
                'decoded' => "a\u228B\uFE00b",
                'encoded' => 'a&vsupne;b',
            ],
            [
                'decoded' => "a\u22AAb",
                'encoded' => 'a&Vvdash;b',
            ],
            [
                'decoded' => "a\u299Ab",
                'encoded' => 'a&vzigzag;b',
            ],
            [
                'decoded' => "a\u0174b",
                'encoded' => 'a&Wcirc;b',
            ],
            [
                'decoded' => "a\u0175b",
                'encoded' => 'a&wcirc;b',
            ],
            [
                'decoded' => "a\u2A5Fb",
                'encoded' => 'a&wedbar;b',
            ],
            [
                'decoded' => "a\u22C0b",
                'encoded' => 'a&Wedge;b',
            ],
            [
                'decoded' => "a\u2259b",
                'encoded' => 'a&wedgeq;b',
            ],
            [
                'decoded' => "a\uD835\uDD1Ab",
                'encoded' => 'a&Wfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD34b",
                'encoded' => 'a&wfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD4Eb",
                'encoded' => 'a&Wopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD68b",
                'encoded' => 'a&wopf;b',
            ],
            [
                'decoded' => "a\u2118b",
                'encoded' => 'a&wp;b',
            ],
            [
                'decoded' => "a\u2240b",
                'encoded' => 'a&wr;b',
            ],
            [
                'decoded' => "a\uD835\uDCB2b",
                'encoded' => 'a&Wscr;b',
            ],
            [
                'decoded' => "a\uD835\uDCCCb",
                'encoded' => 'a&wscr;b',
            ],
            [
                'decoded' => "a\u22C2b",
                'encoded' => 'a&xcap;b',
            ],
            [
                'decoded' => "a\u25EFb",
                'encoded' => 'a&xcirc;b',
            ],
            [
                'decoded' => "a\u22C3b",
                'encoded' => 'a&xcup;b',
            ],
            [
                'decoded' => "a\u25BDb",
                'encoded' => 'a&xdtri;b',
            ],
            [
                'decoded' => "a\uD835\uDD1Bb",
                'encoded' => 'a&Xfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD35b",
                'encoded' => 'a&xfr;b',
            ],
            [
                'decoded' => "a\u27F7b",
                'encoded' => 'a&xharr;b',
            ],
            [
                'decoded' => "a\u27FAb",
                'encoded' => 'a&xhArr;b',
            ],
            [
                'decoded' => "a\u039Eb",
                'encoded' => 'a&Xi;b',
            ],
            [
                'decoded' => "a\u03BEb",
                'encoded' => 'a&xi;b',
            ],
            [
                'decoded' => "a\u27F5b",
                'encoded' => 'a&xlarr;b',
            ],
            [
                'decoded' => "a\u27F8b",
                'encoded' => 'a&xlArr;b',
            ],
            [
                'decoded' => "a\u27FCb",
                'encoded' => 'a&xmap;b',
            ],
            [
                'decoded' => "a\u22FBb",
                'encoded' => 'a&xnis;b',
            ],
            [
                'decoded' => "a\u2A00b",
                'encoded' => 'a&xodot;b',
            ],
            [
                'decoded' => "a\uD835\uDD4Fb",
                'encoded' => 'a&Xopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD69b",
                'encoded' => 'a&xopf;b',
            ],
            [
                'decoded' => "a\u2A01b",
                'encoded' => 'a&xoplus;b',
            ],
            [
                'decoded' => "a\u2A02b",
                'encoded' => 'a&xotime;b',
            ],
            [
                'decoded' => "a\u27F6b",
                'encoded' => 'a&xrarr;b',
            ],
            [
                'decoded' => "a\u27F9b",
                'encoded' => 'a&xrArr;b',
            ],
            [
                'decoded' => "a\uD835\uDCB3b",
                'encoded' => 'a&Xscr;b',
            ],
            [
                'decoded' => "a\uD835\uDCCDb",
                'encoded' => 'a&xscr;b',
            ],
            [
                'decoded' => "a\u2A06b",
                'encoded' => 'a&xsqcup;b',
            ],
            [
                'decoded' => "a\u2A04b",
                'encoded' => 'a&xuplus;b',
            ],
            [
                'decoded' => "a\u25B3b",
                'encoded' => 'a&xutri;b',
            ],
            [
                'decoded' => "a\xDDb",
                'encoded' => 'a&Yacute;b',
            ],
            [
                'decoded' => "a\xFDb",
                'encoded' => 'a&yacute;b',
            ],
            [
                'decoded' => "a\u042Fb",
                'encoded' => 'a&YAcy;b',
            ],
            [
                'decoded' => "a\u044Fb",
                'encoded' => 'a&yacy;b',
            ],
            [
                'decoded' => "a\u0176b",
                'encoded' => 'a&Ycirc;b',
            ],
            [
                'decoded' => "a\u0177b",
                'encoded' => 'a&ycirc;b',
            ],
            [
                'decoded' => "a\u042Bb",
                'encoded' => 'a&Ycy;b',
            ],
            [
                'decoded' => "a\u044Bb",
                'encoded' => 'a&ycy;b',
            ],
            [
                'decoded' => "a\xA5b",
                'encoded' => 'a&yen;b',
            ],
            [
                'decoded' => "a\uD835\uDD1Cb",
                'encoded' => 'a&Yfr;b',
            ],
            [
                'decoded' => "a\uD835\uDD36b",
                'encoded' => 'a&yfr;b',
            ],
            [
                'decoded' => "a\u0407b",
                'encoded' => 'a&YIcy;b',
            ],
            [
                'decoded' => "a\u0457b",
                'encoded' => 'a&yicy;b',
            ],
            [
                'decoded' => "a\uD835\uDD50b",
                'encoded' => 'a&Yopf;b',
            ],
            [
                'decoded' => "a\uD835\uDD6Ab",
                'encoded' => 'a&yopf;b',
            ],
            [
                'decoded' => "a\uD835\uDCB4b",
                'encoded' => 'a&Yscr;b',
            ],
            [
                'decoded' => "a\uD835\uDCCEb",
                'encoded' => 'a&yscr;b',
            ],
            [
                'decoded' => "a\u042Eb",
                'encoded' => 'a&YUcy;b',
            ],
            [
                'decoded' => "a\u044Eb",
                'encoded' => 'a&yucy;b',
            ],
            [
                'decoded' => "a\xFFb",
                'encoded' => 'a&yuml;b',
            ],
            [
                'decoded' => "a\u0178b",
                'encoded' => 'a&Yuml;b',
            ],
            [
                'decoded' => "a\u0179b",
                'encoded' => 'a&Zacute;b',
            ],
            [
                'decoded' => "a\u017Ab",
                'encoded' => 'a&zacute;b',
            ],
            [
                'decoded' => "a\u017Db",
                'encoded' => 'a&Zcaron;b',
            ],
            [
                'decoded' => "a\u017Eb",
                'encoded' => 'a&zcaron;b',
            ],
            [
                'decoded' => "a\u0417b",
                'encoded' => 'a&Zcy;b',
            ],
            [
                'decoded' => "a\u0437b",
                'encoded' => 'a&zcy;b',
            ],
            [
                'decoded' => "a\u017Bb",
                'encoded' => 'a&Zdot;b',
            ],
            [
                'decoded' => "a\u017Cb",
                'encoded' => 'a&zdot;b',
            ],
            [
                'decoded' => "a\u200Bb",
                'encoded' => 'a&ZeroWidthSpace;b',
            ],
            [
                'decoded' => "a\u0396b",
                'encoded' => 'a&Zeta;b',
            ],
            [
                'decoded' => "a\u03B6b",
                'encoded' => 'a&zeta;b',
            ],
            [
                'decoded' => "a\uD835\uDD37b",
                'encoded' => 'a&zfr;b',
            ],
            [
                'decoded' => "a\u2128b",
                'encoded' => 'a&Zfr;b',
            ],
            [
                'decoded' => "a\u0416b",
                'encoded' => 'a&ZHcy;b',
            ],
            [
                'decoded' => "a\u0436b",
                'encoded' => 'a&zhcy;b',
            ],
            [
                'decoded' => "a\u21DDb",
                'encoded' => 'a&zigrarr;b',
            ],
            [
                'decoded' => "a\uD835\uDD6Bb",
                'encoded' => 'a&zopf;b',
            ],
            [
                'decoded' => "a\u2124b",
                'encoded' => 'a&Zopf;b',
            ],
            [
                'decoded' => "a\uD835\uDCB5b",
                'encoded' => 'a&Zscr;b',
            ],
            [
                'decoded' => "a\uD835\uDCCFb",
                'encoded' => 'a&zscr;b',
            ],
            [
                'decoded' => "a\u200Db",
                'encoded' => 'a&zwj;b',
            ],
            [
                'decoded' => "a\u200Cb",
                'encoded' => 'a&zwnj;b',
            ],
            [
                'decoded' => '&xxx; &xxx þ &thorn ¤t &current',
                'encoded' => '&amp;xxx; &amp;xxx &amp;thorn; &amp;thorn &amp;curren;t &amp;current',
            ],
        ];

        foreach ($encodeData as $encodeDataInnerArray) {
            static::assertSame(
                UTF8::to_utf8($encodeDataInnerArray['decoded']),
                UTF8::html_decode($encodeDataInnerArray['encoded']),
                'tested: ' . \print_r($encodeDataInnerArray, true)
            );
        }
    }

    public function testHtmlEncode2()
    {
        $encodeData = [
            0 => [
                'encoded' => 'a&Aacute;b',
                'decoded' => 'aÁb',
            ],
            1 => [
                'encoded' => 'a&aacute;b',
                'decoded' => 'aáb',
            ],
            2 => [
                'encoded' => 'a&Abreve;b',
                'decoded' => 'aĂb',
            ],
            3 => [
                'encoded' => 'a&abreve;b',
                'decoded' => 'aăb',
            ],
            4 => [
                'encoded' => 'a&ac;b',
                'decoded' => 'a∾b',
            ],
            5 => [
                'encoded' => 'a&acd;b',
                'decoded' => 'a∿b',
            ],
            6 => [
                'encoded' => 'a&acE;b',
                'decoded' => 'a∾̳b',
            ],
            7 => [
                'encoded' => 'a&Acirc;b',
                'decoded' => 'aÂb',
            ],
            8 => [
                'encoded' => 'a&acirc;b',
                'decoded' => 'aâb',
            ],
            9 => [
                'encoded' => 'a&acute;b',
                'decoded' => 'a´b',
            ],
            10 => [
                'encoded' => 'a&Acy;b',
                'decoded' => 'aАb',
            ],
            11 => [
                'encoded' => 'a&acy;b',
                'decoded' => 'aаb',
            ],
            12 => [
                'encoded' => 'a&AElig;b',
                'decoded' => 'aÆb',
            ],
            13 => [
                'encoded' => 'a&aelig;b',
                'decoded' => 'aæb',
            ],
            14 => [
                'encoded' => 'a&af;b',
                'decoded' => 'a⁡b',
            ],
            15 => [
                'encoded' => 'a&Afr;b',
                'decoded' => 'a𝔄b',
            ],
            16 => [
                'encoded' => 'a&afr;b',
                'decoded' => 'a𝔞b',
            ],
            17 => [
                'encoded' => 'a&Agrave;b',
                'decoded' => 'aÀb',
            ],
            18 => [
                'encoded' => 'a&agrave;b',
                'decoded' => 'aàb',
            ],
            19 => [
                'encoded' => 'a&aleph;b',
                'decoded' => 'aℵb',
            ],
            20 => [
                'encoded' => 'a&Alpha;b',
                'decoded' => 'aΑb',
            ],
            21 => [
                'encoded' => 'a&alpha;b',
                'decoded' => 'aαb',
            ],
            22 => [
                'encoded' => 'a&Amacr;b',
                'decoded' => 'aĀb',
            ],
            23 => [
                'encoded' => 'a&amacr;b',
                'decoded' => 'aāb',
            ],
            24 => [
                'encoded' => 'a&amalg;b',
                'decoded' => 'a⨿b',
            ],
            25 => [
                'encoded' => 'a&amp;b',
                'decoded' => 'a&b',
            ],
            26 => [
                'encoded' => 'a&andand;b',
                'decoded' => 'a⩕b',
            ],
            27 => [
                'encoded' => 'a&And;b',
                'decoded' => 'a⩓b',
            ],
            28 => [
                'encoded' => 'a&and;b',
                'decoded' => 'a∧b',
            ],
            29 => [
                'encoded' => 'a&andd;b',
                'decoded' => 'a⩜b',
            ],
            30 => [
                'encoded' => 'a&andslope;b',
                'decoded' => 'a⩘b',
            ],
            31 => [
                'encoded' => 'a&andv;b',
                'decoded' => 'a⩚b',
            ],
            32 => [
                'encoded' => 'a&ang;b',
                'decoded' => 'a∠b',
            ],
            33 => [
                'encoded' => 'a&ange;b',
                'decoded' => 'a⦤b',
            ],
            34 => [
                'encoded' => 'a&angmsdaa;b',
                'decoded' => 'a⦨b',
            ],
            35 => [
                'encoded' => 'a&angmsdab;b',
                'decoded' => 'a⦩b',
            ],
            36 => [
                'encoded' => 'a&angmsdac;b',
                'decoded' => 'a⦪b',
            ],
            37 => [
                'encoded' => 'a&angmsdad;b',
                'decoded' => 'a⦫b',
            ],
            38 => [
                'encoded' => 'a&angmsdae;b',
                'decoded' => 'a⦬b',
            ],
            39 => [
                'encoded' => 'a&angmsdaf;b',
                'decoded' => 'a⦭b',
            ],
            40 => [
                'encoded' => 'a&angmsdag;b',
                'decoded' => 'a⦮b',
            ],
            41 => [
                'encoded' => 'a&angmsdah;b',
                'decoded' => 'a⦯b',
            ],
            42 => [
                'encoded' => 'a&angmsd;b',
                'decoded' => 'a∡b',
            ],
            43 => [
                'encoded' => 'a&angrt;b',
                'decoded' => 'a∟b',
            ],
            44 => [
                'encoded' => 'a&angrtvb;b',
                'decoded' => 'a⊾b',
            ],
            45 => [
                'encoded' => 'a&angrtvbd;b',
                'decoded' => 'a⦝b',
            ],
            46 => [
                'encoded' => 'a&angsph;b',
                'decoded' => 'a∢b',
            ],
            47 => [
                'encoded' => 'a&angst;b',
                'decoded' => 'aÅb',
            ],
            48 => [
                'encoded' => 'a&angzarr;b',
                'decoded' => 'a⍼b',
            ],
            49 => [
                'encoded' => 'a&Aogon;b',
                'decoded' => 'aĄb',
            ],
            50 => [
                'encoded' => 'a&aogon;b',
                'decoded' => 'aąb',
            ],
            51 => [
                'encoded' => 'a&Aopf;b',
                'decoded' => 'a𝔸b',
            ],
            52 => [
                'encoded' => 'a&aopf;b',
                'decoded' => 'a𝕒b',
            ],
            53 => [
                'encoded' => 'a&apacir;b',
                'decoded' => 'a⩯b',
            ],
            54 => [
                'encoded' => 'a&ap;b',
                'decoded' => 'a≈b',
            ],
            55 => [
                'encoded' => 'a&apE;b',
                'decoded' => 'a⩰b',
            ],
            56 => [
                'encoded' => 'a&ape;b',
                'decoded' => 'a≊b',
            ],
            57 => [
                'encoded' => 'a&apid;b',
                'decoded' => 'a≋b',
            ],
            58 => [
                'encoded' => 'a&apos;b',
                'decoded' => 'a\'b',
            ],
            59 => [
                'encoded' => 'a&aring;b',
                'decoded' => 'aåb',
            ],
            60 => [
                'encoded' => 'a&Ascr;b',
                'decoded' => 'a𝒜b',
            ],
            61 => [
                'encoded' => 'a&ascr;b',
                'decoded' => 'a𝒶b',
            ],
            62 => [
                'encoded' => 'a&Atilde;b',
                'decoded' => 'aÃb',
            ],
            63 => [
                'encoded' => 'a&atilde;b',
                'decoded' => 'aãb',
            ],
            64 => [
                'encoded' => 'a&Auml;b',
                'decoded' => 'aÄb',
            ],
            65 => [
                'encoded' => 'a&auml;b',
                'decoded' => 'aäb',
            ],
            66 => [
                'encoded' => 'a&awconint;b',
                'decoded' => 'a∳b',
            ],
            67 => [
                'encoded' => 'a&awint;b',
                'decoded' => 'a⨑b',
            ],
            68 => [
                'encoded' => 'a&Barv;b',
                'decoded' => 'a⫧b',
            ],
            69 => [
                'encoded' => 'a&barvee;b',
                'decoded' => 'a⊽b',
            ],
            70 => [
                'encoded' => 'a&barwed;b',
                'decoded' => 'a⌅b',
            ],
            71 => [
                'encoded' => 'a&Barwed;b',
                'decoded' => 'a⌆b',
            ],
            72 => [
                'encoded' => 'a&bbrk;b',
                'decoded' => 'a⎵b',
            ],
            73 => [
                'encoded' => 'a&bbrktbrk;b',
                'decoded' => 'a⎶b',
            ],
            74 => [
                'encoded' => 'a&bcong;b',
                'decoded' => 'a≌b',
            ],
            75 => [
                'encoded' => 'a&Bcy;b',
                'decoded' => 'aБb',
            ],
            76 => [
                'encoded' => 'a&bcy;b',
                'decoded' => 'aбb',
            ],
            77 => [
                'encoded' => 'a&bdquo;b',
                'decoded' => 'a„b',
            ],
            78 => [
                'encoded' => 'a&becaus;b',
                'decoded' => 'a∵b',
            ],
            79 => [
                'encoded' => 'a&bemptyv;b',
                'decoded' => 'a⦰b',
            ],
            80 => [
                'encoded' => 'a&bepsi;b',
                'decoded' => 'a϶b',
            ],
            81 => [
                'encoded' => 'a&Beta;b',
                'decoded' => 'aΒb',
            ],
            82 => [
                'encoded' => 'a&beta;b',
                'decoded' => 'aβb',
            ],
            83 => [
                'encoded' => 'a&beth;b',
                'decoded' => 'aℶb',
            ],
            84 => [
                'encoded' => 'a&Bfr;b',
                'decoded' => 'a𝔅b',
            ],
            85 => [
                'encoded' => 'a&bfr;b',
                'decoded' => 'a𝔟b',
            ],
            86 => [
                'encoded' => 'a&blank;b',
                'decoded' => 'a␣b',
            ],
            87 => [
                'encoded' => 'a&blk12;b',
                'decoded' => 'a▒b',
            ],
            88 => [
                'encoded' => 'a&blk14;b',
                'decoded' => 'a░b',
            ],
            89 => [
                'encoded' => 'a&blk34;b',
                'decoded' => 'a▓b',
            ],
            90 => [
                'encoded' => 'a&block;b',
                'decoded' => 'a█b',
            ],
            91 => [
                'encoded' => 'a&bne;b',
                'decoded' => 'a=⃥b',
            ],
            92 => [
                'encoded' => 'a&bnequiv;b',
                'decoded' => 'a≡⃥b',
            ],
            93 => [
                'encoded' => 'a&bNot;b',
                'decoded' => 'a⫭b',
            ],
            94 => [
                'encoded' => 'a&bnot;b',
                'decoded' => 'a⌐b',
            ],
            95 => [
                'encoded' => 'a&Bopf;b',
                'decoded' => 'a𝔹b',
            ],
            96 => [
                'encoded' => 'a&bopf;b',
                'decoded' => 'a𝕓b',
            ],
            97 => [
                'encoded' => 'a&bot;b',
                'decoded' => 'a⊥b',
            ],
            98 => [
                'encoded' => 'a&bowtie;b',
                'decoded' => 'a⋈b',
            ],
            99 => [
                'encoded' => 'a&boxbox;b',
                'decoded' => 'a⧉b',
            ],
            100 => [
                'encoded' => 'a&boxdl;b',
                'decoded' => 'a┐b',
            ],
            101 => [
                'encoded' => 'a&boxdL;b',
                'decoded' => 'a╕b',
            ],
            102 => [
                'encoded' => 'a&boxDl;b',
                'decoded' => 'a╖b',
            ],
            103 => [
                'encoded' => 'a&boxDL;b',
                'decoded' => 'a╗b',
            ],
            104 => [
                'encoded' => 'a&boxdr;b',
                'decoded' => 'a┌b',
            ],
            105 => [
                'encoded' => 'a&boxdR;b',
                'decoded' => 'a╒b',
            ],
            106 => [
                'encoded' => 'a&boxDr;b',
                'decoded' => 'a╓b',
            ],
            107 => [
                'encoded' => 'a&boxDR;b',
                'decoded' => 'a╔b',
            ],
            108 => [
                'encoded' => 'a&boxh;b',
                'decoded' => 'a─b',
            ],
            109 => [
                'encoded' => 'a&boxH;b',
                'decoded' => 'a═b',
            ],
            110 => [
                'encoded' => 'a&boxhd;b',
                'decoded' => 'a┬b',
            ],
            111 => [
                'encoded' => 'a&boxHd;b',
                'decoded' => 'a╤b',
            ],
            112 => [
                'encoded' => 'a&boxhD;b',
                'decoded' => 'a╥b',
            ],
            113 => [
                'encoded' => 'a&boxHD;b',
                'decoded' => 'a╦b',
            ],
            114 => [
                'encoded' => 'a&boxhu;b',
                'decoded' => 'a┴b',
            ],
            115 => [
                'encoded' => 'a&boxHu;b',
                'decoded' => 'a╧b',
            ],
            116 => [
                'encoded' => 'a&boxhU;b',
                'decoded' => 'a╨b',
            ],
            117 => [
                'encoded' => 'a&boxHU;b',
                'decoded' => 'a╩b',
            ],
            118 => [
                'encoded' => 'a&boxul;b',
                'decoded' => 'a┘b',
            ],
            119 => [
                'encoded' => 'a&boxuL;b',
                'decoded' => 'a╛b',
            ],
            120 => [
                'encoded' => 'a&boxUl;b',
                'decoded' => 'a╜b',
            ],
            121 => [
                'encoded' => 'a&boxUL;b',
                'decoded' => 'a╝b',
            ],
            122 => [
                'encoded' => 'a&boxur;b',
                'decoded' => 'a└b',
            ],
            123 => [
                'encoded' => 'a&boxuR;b',
                'decoded' => 'a╘b',
            ],
            124 => [
                'encoded' => 'a&boxUr;b',
                'decoded' => 'a╙b',
            ],
            125 => [
                'encoded' => 'a&boxUR;b',
                'decoded' => 'a╚b',
            ],
            126 => [
                'encoded' => 'a&boxv;b',
                'decoded' => 'a│b',
            ],
            127 => [
                'encoded' => 'a&boxV;b',
                'decoded' => 'a║b',
            ],
            128 => [
                'encoded' => 'a&boxvh;b',
                'decoded' => 'a┼b',
            ],
            129 => [
                'encoded' => 'a&boxvH;b',
                'decoded' => 'a╪b',
            ],
            130 => [
                'encoded' => 'a&boxVh;b',
                'decoded' => 'a╫b',
            ],
            131 => [
                'encoded' => 'a&boxVH;b',
                'decoded' => 'a╬b',
            ],
            132 => [
                'encoded' => 'a&boxvl;b',
                'decoded' => 'a┤b',
            ],
            133 => [
                'encoded' => 'a&boxvL;b',
                'decoded' => 'a╡b',
            ],
            134 => [
                'encoded' => 'a&boxVl;b',
                'decoded' => 'a╢b',
            ],
            135 => [
                'encoded' => 'a&boxVL;b',
                'decoded' => 'a╣b',
            ],
            136 => [
                'encoded' => 'a&boxvr;b',
                'decoded' => 'a├b',
            ],
            137 => [
                'encoded' => 'a&boxvR;b',
                'decoded' => 'a╞b',
            ],
            138 => [
                'encoded' => 'a&boxVr;b',
                'decoded' => 'a╟b',
            ],
            139 => [
                'encoded' => 'a&boxVR;b',
                'decoded' => 'a╠b',
            ],
            140 => [
                'encoded' => 'a&bprime;b',
                'decoded' => 'a‵b',
            ],
            141 => [
                'encoded' => 'a&breve;b',
                'decoded' => 'a˘b',
            ],
            142 => [
                'encoded' => 'a&brvbar;b',
                'decoded' => 'a¦b',
            ],
            143 => [
                'encoded' => 'a&bscr;b',
                'decoded' => 'a𝒷b',
            ],
            144 => [
                'encoded' => 'a&Bscr;b',
                'decoded' => 'aℬb',
            ],
            145 => [
                'encoded' => 'a&bsemi;b',
                'decoded' => 'a⁏b',
            ],
            146 => [
                'encoded' => 'a&bsim;b',
                'decoded' => 'a∽b',
            ],
            147 => [
                'encoded' => 'a&bsime;b',
                'decoded' => 'a⋍b',
            ],
            148 => [
                'encoded' => 'a&bsolb;b',
                'decoded' => 'a⧅b',
            ],
            149 => [
                'encoded' => 'a&bsolhsub;b',
                'decoded' => 'a⟈b',
            ],
            150 => [
                'encoded' => 'a&bull;b',
                'decoded' => 'a•b',
            ],
            151 => [
                'encoded' => 'a&bump;b',
                'decoded' => 'a≎b',
            ],
            152 => [
                'encoded' => 'a&bumpE;b',
                'decoded' => 'a⪮b',
            ],
            153 => [
                'encoded' => 'a&bumpe;b',
                'decoded' => 'a≏b',
            ],
            154 => [
                'encoded' => 'a&Cacute;b',
                'decoded' => 'aĆb',
            ],
            155 => [
                'encoded' => 'a&cacute;b',
                'decoded' => 'aćb',
            ],
            156 => [
                'encoded' => 'a&capand;b',
                'decoded' => 'a⩄b',
            ],
            157 => [
                'encoded' => 'a&capbrcup;b',
                'decoded' => 'a⩉b',
            ],
            158 => [
                'encoded' => 'a&capcap;b',
                'decoded' => 'a⩋b',
            ],
            159 => [
                'encoded' => 'a&cap;b',
                'decoded' => 'a∩b',
            ],
            160 => [
                'encoded' => 'a&Cap;b',
                'decoded' => 'a⋒b',
            ],
            161 => [
                'encoded' => 'a&capcup;b',
                'decoded' => 'a⩇b',
            ],
            162 => [
                'encoded' => 'a&capdot;b',
                'decoded' => 'a⩀b',
            ],
            163 => [
                'encoded' => 'a&caps;b',
                'decoded' => 'a∩︀b',
            ],
            164 => [
                'encoded' => 'a&caret;b',
                'decoded' => 'a⁁b',
            ],
            165 => [
                'encoded' => 'a&caron;b',
                'decoded' => 'aˇb',
            ],
            166 => [
                'encoded' => 'a&ccaps;b',
                'decoded' => 'a⩍b',
            ],
            167 => [
                'encoded' => 'a&Ccaron;b',
                'decoded' => 'aČb',
            ],
            168 => [
                'encoded' => 'a&ccaron;b',
                'decoded' => 'ačb',
            ],
            169 => [
                'encoded' => 'a&Ccedil;b',
                'decoded' => 'aÇb',
            ],
            170 => [
                'encoded' => 'a&ccedil;b',
                'decoded' => 'açb',
            ],
            171 => [
                'encoded' => 'a&Ccirc;b',
                'decoded' => 'aĈb',
            ],
            172 => [
                'encoded' => 'a&ccirc;b',
                'decoded' => 'aĉb',
            ],
            173 => [
                'encoded' => 'a&Cconint;b',
                'decoded' => 'a∰b',
            ],
            174 => [
                'encoded' => 'a&ccups;b',
                'decoded' => 'a⩌b',
            ],
            175 => [
                'encoded' => 'a&ccupssm;b',
                'decoded' => 'a⩐b',
            ],
            176 => [
                'encoded' => 'a&Cdot;b',
                'decoded' => 'aĊb',
            ],
            177 => [
                'encoded' => 'a&cdot;b',
                'decoded' => 'aċb',
            ],
            178 => [
                'encoded' => 'a&cedil;b',
                'decoded' => 'a¸b',
            ],
            179 => [
                'encoded' => 'a&cemptyv;b',
                'decoded' => 'a⦲b',
            ],
            180 => [
                'encoded' => 'a&cent;b',
                'decoded' => 'a¢b',
            ],
            181 => [
                'encoded' => 'a&cfr;b',
                'decoded' => 'a𝔠b',
            ],
            182 => [
                'encoded' => 'a&Cfr;b',
                'decoded' => 'aℭb',
            ],
            183 => [
                'encoded' => 'a&CHcy;b',
                'decoded' => 'aЧb',
            ],
            184 => [
                'encoded' => 'a&chcy;b',
                'decoded' => 'aчb',
            ],
            185 => [
                'encoded' => 'a&check;b',
                'decoded' => 'a✓b',
            ],
            186 => [
                'encoded' => 'a&Chi;b',
                'decoded' => 'aΧb',
            ],
            187 => [
                'encoded' => 'a&chi;b',
                'decoded' => 'aχb',
            ],
            188 => [
                'encoded' => 'a&circ;b',
                'decoded' => 'aˆb',
            ],
            189 => [
                'encoded' => 'a&cir;b',
                'decoded' => 'a○b',
            ],
            190 => [
                'encoded' => 'a&cirE;b',
                'decoded' => 'a⧃b',
            ],
            191 => [
                'encoded' => 'a&cire;b',
                'decoded' => 'a≗b',
            ],
            192 => [
                'encoded' => 'a&cirfnint;b',
                'decoded' => 'a⨐b',
            ],
            193 => [
                'encoded' => 'a&cirmid;b',
                'decoded' => 'a⫯b',
            ],
            194 => [
                'encoded' => 'a&cirscir;b',
                'decoded' => 'a⧂b',
            ],
            195 => [
                'encoded' => 'a&clubs;b',
                'decoded' => 'a♣b',
            ],
            196 => [
                'encoded' => 'a&Colon;b',
                'decoded' => 'a∷b',
            ],
            197 => [
                'encoded' => 'a&Colone;b',
                'decoded' => 'a⩴b',
            ],
            198 => [
                'encoded' => 'a&colone;b',
                'decoded' => 'a≔b',
            ],
            199 => [
                'encoded' => 'a&comp;b',
                'decoded' => 'a∁b',
            ],
            200 => [
                'encoded' => 'a&compfn;b',
                'decoded' => 'a∘b',
            ],
            201 => [
                'encoded' => 'a&cong;b',
                'decoded' => 'a≅b',
            ],
            202 => [
                'encoded' => 'a&congdot;b',
                'decoded' => 'a⩭b',
            ],
            203 => [
                'encoded' => 'a&Conint;b',
                'decoded' => 'a∯b',
            ],
            204 => [
                'encoded' => 'a&copf;b',
                'decoded' => 'a𝕔b',
            ],
            205 => [
                'encoded' => 'a&Copf;b',
                'decoded' => 'aℂb',
            ],
            206 => [
                'encoded' => 'a&coprod;b',
                'decoded' => 'a∐b',
            ],
            207 => [
                'encoded' => 'a&copy;b',
                'decoded' => 'a©b',
            ],
            208 => [
                'encoded' => 'a&copysr;b',
                'decoded' => 'a℗b',
            ],
            209 => [
                'encoded' => 'a&crarr;b',
                'decoded' => 'a↵b',
            ],
            210 => [
                'encoded' => 'a&cross;b',
                'decoded' => 'a✗b',
            ],
            211 => [
                'encoded' => 'a&Cross;b',
                'decoded' => 'a⨯b',
            ],
            212 => [
                'encoded' => 'a&Cscr;b',
                'decoded' => 'a𝒞b',
            ],
            213 => [
                'encoded' => 'a&cscr;b',
                'decoded' => 'a𝒸b',
            ],
            214 => [
                'encoded' => 'a&csub;b',
                'decoded' => 'a⫏b',
            ],
            215 => [
                'encoded' => 'a&csube;b',
                'decoded' => 'a⫑b',
            ],
            216 => [
                'encoded' => 'a&csup;b',
                'decoded' => 'a⫐b',
            ],
            217 => [
                'encoded' => 'a&csupe;b',
                'decoded' => 'a⫒b',
            ],
            218 => [
                'encoded' => 'a&ctdot;b',
                'decoded' => 'a⋯b',
            ],
            219 => [
                'encoded' => 'a&cudarrl;b',
                'decoded' => 'a⤸b',
            ],
            220 => [
                'encoded' => 'a&cudarrr;b',
                'decoded' => 'a⤵b',
            ],
            221 => [
                'encoded' => 'a&cuepr;b',
                'decoded' => 'a⋞b',
            ],
            222 => [
                'encoded' => 'a&cuesc;b',
                'decoded' => 'a⋟b',
            ],
            223 => [
                'encoded' => 'a&cularr;b',
                'decoded' => 'a↶b',
            ],
            224 => [
                'encoded' => 'a&cularrp;b',
                'decoded' => 'a⤽b',
            ],
            225 => [
                'encoded' => 'a&cupbrcap;b',
                'decoded' => 'a⩈b',
            ],
            226 => [
                'encoded' => 'a&cupcap;b',
                'decoded' => 'a⩆b',
            ],
            227 => [
                'encoded' => 'a&CupCap;b',
                'decoded' => 'a≍b',
            ],
            228 => [
                'encoded' => 'a&cup;b',
                'decoded' => 'a∪b',
            ],
            229 => [
                'encoded' => 'a&Cup;b',
                'decoded' => 'a⋓b',
            ],
            230 => [
                'encoded' => 'a&cupcup;b',
                'decoded' => 'a⩊b',
            ],
            231 => [
                'encoded' => 'a&cupdot;b',
                'decoded' => 'a⊍b',
            ],
            232 => [
                'encoded' => 'a&cupor;b',
                'decoded' => 'a⩅b',
            ],
            233 => [
                'encoded' => 'a&cups;b',
                'decoded' => 'a∪︀b',
            ],
            234 => [
                'encoded' => 'a&curarr;b',
                'decoded' => 'a↷b',
            ],
            235 => [
                'encoded' => 'a&curarrm;b',
                'decoded' => 'a⤼b',
            ],
            236 => [
                'encoded' => 'a&curren;b',
                'decoded' => 'a¤b',
            ],
            237 => [
                'encoded' => 'a&cuvee;b',
                'decoded' => 'a⋎b',
            ],
            238 => [
                'encoded' => 'a&cuwed;b',
                'decoded' => 'a⋏b',
            ],
            239 => [
                'encoded' => 'a&cwconint;b',
                'decoded' => 'a∲b',
            ],
            240 => [
                'encoded' => 'a&cwint;b',
                'decoded' => 'a∱b',
            ],
            241 => [
                'encoded' => 'a&cylcty;b',
                'decoded' => 'a⌭b',
            ],
            242 => [
                'encoded' => 'a&dagger;b',
                'decoded' => 'a†b',
            ],
            243 => [
                'encoded' => 'a&Dagger;b',
                'decoded' => 'a‡b',
            ],
            244 => [
                'encoded' => 'a&daleth;b',
                'decoded' => 'aℸb',
            ],
            245 => [
                'encoded' => 'a&darr;b',
                'decoded' => 'a↓b',
            ],
            246 => [
                'encoded' => 'a&Darr;b',
                'decoded' => 'a↡b',
            ],
            247 => [
                'encoded' => 'a&dArr;b',
                'decoded' => 'a⇓b',
            ],
            248 => [
                'encoded' => 'a&dash;b',
                'decoded' => 'a‐b',
            ],
            249 => [
                'encoded' => 'a&Dashv;b',
                'decoded' => 'a⫤b',
            ],
            250 => [
                'encoded' => 'a&dashv;b',
                'decoded' => 'a⊣b',
            ],
            251 => [
                'encoded' => 'a&dblac;b',
                'decoded' => 'a˝b',
            ],
            252 => [
                'encoded' => 'a&Dcaron;b',
                'decoded' => 'aĎb',
            ],
            253 => [
                'encoded' => 'a&dcaron;b',
                'decoded' => 'aďb',
            ],
            254 => [
                'encoded' => 'a&Dcy;b',
                'decoded' => 'aДb',
            ],
            255 => [
                'encoded' => 'a&dcy;b',
                'decoded' => 'aдb',
            ],
            256 => [
                'encoded' => 'a&ddarr;b',
                'decoded' => 'a⇊b',
            ],
            257 => [
                'encoded' => 'a&DD;b',
                'decoded' => 'aⅅb',
            ],
            258 => [
                'encoded' => 'a&dd;b',
                'decoded' => 'aⅆb',
            ],
            259 => [
                'encoded' => 'a&DDotrahd;b',
                'decoded' => 'a⤑b',
            ],
            260 => [
                'encoded' => 'a&deg;b',
                'decoded' => 'a°b',
            ],
            261 => [
                'encoded' => 'a&Del;b',
                'decoded' => 'a∇b',
            ],
            262 => [
                'encoded' => 'a&Delta;b',
                'decoded' => 'aΔb',
            ],
            263 => [
                'encoded' => 'a&delta;b',
                'decoded' => 'aδb',
            ],
            264 => [
                'encoded' => 'a&demptyv;b',
                'decoded' => 'a⦱b',
            ],
            265 => [
                'encoded' => 'a&dfisht;b',
                'decoded' => 'a⥿b',
            ],
            266 => [
                'encoded' => 'a&Dfr;b',
                'decoded' => 'a𝔇b',
            ],
            267 => [
                'encoded' => 'a&dfr;b',
                'decoded' => 'a𝔡b',
            ],
            268 => [
                'encoded' => 'a&dHar;b',
                'decoded' => 'a⥥b',
            ],
            269 => [
                'encoded' => 'a&dharl;b',
                'decoded' => 'a⇃b',
            ],
            270 => [
                'encoded' => 'a&dharr;b',
                'decoded' => 'a⇂b',
            ],
            271 => [
                'encoded' => 'a&diam;b',
                'decoded' => 'a⋄b',
            ],
            272 => [
                'encoded' => 'a&diams;b',
                'decoded' => 'a♦b',
            ],
            273 => [
                'encoded' => 'a&die;b',
                'decoded' => 'a¨b',
            ],
            274 => [
                'encoded' => 'a&disin;b',
                'decoded' => 'a⋲b',
            ],
            275 => [
                'encoded' => 'a&div;b',
                'decoded' => 'a÷b',
            ],
            276 => [
                'encoded' => 'a&divonx;b',
                'decoded' => 'a⋇b',
            ],
            277 => [
                'encoded' => 'a&DJcy;b',
                'decoded' => 'aЂb',
            ],
            278 => [
                'encoded' => 'a&djcy;b',
                'decoded' => 'aђb',
            ],
            279 => [
                'encoded' => 'a&dlcorn;b',
                'decoded' => 'a⌞b',
            ],
            280 => [
                'encoded' => 'a&dlcrop;b',
                'decoded' => 'a⌍b',
            ],
            281 => [
                'encoded' => 'a&Dopf;b',
                'decoded' => 'a𝔻b',
            ],
            282 => [
                'encoded' => 'a&dopf;b',
                'decoded' => 'a𝕕b',
            ],
            283 => [
                'encoded' => 'a&dot;b',
                'decoded' => 'a˙b',
            ],
            284 => [
                'encoded' => 'a&DotDot;b',
                'decoded' => 'a⃜b',
            ],
            285 => [
                'encoded' => 'a&doteq;b',
                'decoded' => 'a≐b',
            ],
            286 => [
                'encoded' => 'a&DownArrowBar;b',
                'decoded' => 'a⤓b',
            ],
            287 => [
                'encoded' => 'a&DownBreve;b',
                'decoded' => 'ȃb',
            ],
            288 => [
                'encoded' => 'a&DownLeftRightVector;b',
                'decoded' => 'a⥐b',
            ],
            289 => [
                'encoded' => 'a&DownLeftTeeVector;b',
                'decoded' => 'a⥞b',
            ],
            290 => [
                'encoded' => 'a&DownLeftVectorBar;b',
                'decoded' => 'a⥖b',
            ],
            291 => [
                'encoded' => 'a&DownRightTeeVector;b',
                'decoded' => 'a⥟b',
            ],
            292 => [
                'encoded' => 'a&DownRightVectorBar;b',
                'decoded' => 'a⥗b',
            ],
            293 => [
                'encoded' => 'a&drcorn;b',
                'decoded' => 'a⌟b',
            ],
            294 => [
                'encoded' => 'a&drcrop;b',
                'decoded' => 'a⌌b',
            ],
            295 => [
                'encoded' => 'a&Dscr;b',
                'decoded' => 'a𝒟b',
            ],
            296 => [
                'encoded' => 'a&dscr;b',
                'decoded' => 'a𝒹b',
            ],
            297 => [
                'encoded' => 'a&DScy;b',
                'decoded' => 'aЅb',
            ],
            298 => [
                'encoded' => 'a&dscy;b',
                'decoded' => 'aѕb',
            ],
            299 => [
                'encoded' => 'a&dsol;b',
                'decoded' => 'a⧶b',
            ],
            300 => [
                'encoded' => 'a&Dstrok;b',
                'decoded' => 'aĐb',
            ],
            301 => [
                'encoded' => 'a&dstrok;b',
                'decoded' => 'ađb',
            ],
            302 => [
                'encoded' => 'a&dtdot;b',
                'decoded' => 'a⋱b',
            ],
            303 => [
                'encoded' => 'a&dtri;b',
                'decoded' => 'a▿b',
            ],
            304 => [
                'encoded' => 'a&dtrif;b',
                'decoded' => 'a▾b',
            ],
            305 => [
                'encoded' => 'a&duarr;b',
                'decoded' => 'a⇵b',
            ],
            306 => [
                'encoded' => 'a&duhar;b',
                'decoded' => 'a⥯b',
            ],
            307 => [
                'encoded' => 'a&dwangle;b',
                'decoded' => 'a⦦b',
            ],
            308 => [
                'encoded' => 'a&DZcy;b',
                'decoded' => 'aЏb',
            ],
            309 => [
                'encoded' => 'a&dzcy;b',
                'decoded' => 'aџb',
            ],
            310 => [
                'encoded' => 'a&dzigrarr;b',
                'decoded' => 'a⟿b',
            ],
            311 => [
                'encoded' => 'a&Eacute;b',
                'decoded' => 'aÉb',
            ],
            312 => [
                'encoded' => 'a&eacute;b',
                'decoded' => 'aéb',
            ],
            313 => [
                'encoded' => 'a&easter;b',
                'decoded' => 'a⩮b',
            ],
            314 => [
                'encoded' => 'a&Ecaron;b',
                'decoded' => 'aĚb',
            ],
            315 => [
                'encoded' => 'a&ecaron;b',
                'decoded' => 'aěb',
            ],
            316 => [
                'encoded' => 'a&Ecirc;b',
                'decoded' => 'aÊb',
            ],
            317 => [
                'encoded' => 'a&ecirc;b',
                'decoded' => 'aêb',
            ],
            318 => [
                'encoded' => 'a&ecir;b',
                'decoded' => 'a≖b',
            ],
            319 => [
                'encoded' => 'a&ecolon;b',
                'decoded' => 'a≕b',
            ],
            320 => [
                'encoded' => 'a&Ecy;b',
                'decoded' => 'aЭb',
            ],
            321 => [
                'encoded' => 'a&ecy;b',
                'decoded' => 'aэb',
            ],
            322 => [
                'encoded' => 'a&eDDot;b',
                'decoded' => 'a⩷b',
            ],
            323 => [
                'encoded' => 'a&Edot;b',
                'decoded' => 'aĖb',
            ],
            324 => [
                'encoded' => 'a&edot;b',
                'decoded' => 'aėb',
            ],
            325 => [
                'encoded' => 'a&eDot;b',
                'decoded' => 'a≑b',
            ],
            326 => [
                'encoded' => 'a&ee;b',
                'decoded' => 'aⅇb',
            ],
            327 => [
                'encoded' => 'a&efDot;b',
                'decoded' => 'a≒b',
            ],
            328 => [
                'encoded' => 'a&Efr;b',
                'decoded' => 'a𝔈b',
            ],
            329 => [
                'encoded' => 'a&efr;b',
                'decoded' => 'a𝔢b',
            ],
            330 => [
                'encoded' => 'a&eg;b',
                'decoded' => 'a⪚b',
            ],
            331 => [
                'encoded' => 'a&Egrave;b',
                'decoded' => 'aÈb',
            ],
            332 => [
                'encoded' => 'a&egrave;b',
                'decoded' => 'aèb',
            ],
            333 => [
                'encoded' => 'a&egs;b',
                'decoded' => 'a⪖b',
            ],
            334 => [
                'encoded' => 'a&egsdot;b',
                'decoded' => 'a⪘b',
            ],
            335 => [
                'encoded' => 'a&el;b',
                'decoded' => 'a⪙b',
            ],
            336 => [
                'encoded' => 'a&elinters;b',
                'decoded' => 'a⏧b',
            ],
            337 => [
                'encoded' => 'a&ell;b',
                'decoded' => 'aℓb',
            ],
            338 => [
                'encoded' => 'a&els;b',
                'decoded' => 'a⪕b',
            ],
            339 => [
                'encoded' => 'a&elsdot;b',
                'decoded' => 'a⪗b',
            ],
            340 => [
                'encoded' => 'a&Emacr;b',
                'decoded' => 'aĒb',
            ],
            341 => [
                'encoded' => 'a&emacr;b',
                'decoded' => 'aēb',
            ],
            342 => [
                'encoded' => 'a&empty;b',
                'decoded' => 'a∅b',
            ],
            343 => [
                'encoded' => 'a&EmptySmallSquare;b',
                'decoded' => 'a◻b',
            ],
            344 => [
                'encoded' => 'a&EmptyVerySmallSquare;b',
                'decoded' => 'a▫b',
            ],
            345 => [
                'encoded' => 'a&emsp13;b',
                'decoded' => 'a b',
            ],
            346 => [
                'encoded' => 'a&emsp14;b',
                'decoded' => 'a b',
            ],
            347 => [
                'encoded' => 'a&emsp;b',
                'decoded' => 'a b',
            ],
            348 => [
                'encoded' => 'a&ENG;b',
                'decoded' => 'aŊb',
            ],
            349 => [
                'encoded' => 'a&eng;b',
                'decoded' => 'aŋb',
            ],
            350 => [
                'encoded' => 'a&ensp;b',
                'decoded' => 'a b',
            ],
            351 => [
                'encoded' => 'a&Eogon;b',
                'decoded' => 'aĘb',
            ],
            352 => [
                'encoded' => 'a&eogon;b',
                'decoded' => 'aęb',
            ],
            353 => [
                'encoded' => 'a&Eopf;b',
                'decoded' => 'a𝔼b',
            ],
            354 => [
                'encoded' => 'a&eopf;b',
                'decoded' => 'a𝕖b',
            ],
            355 => [
                'encoded' => 'a&epar;b',
                'decoded' => 'a⋕b',
            ],
            356 => [
                'encoded' => 'a&eparsl;b',
                'decoded' => 'a⧣b',
            ],
            357 => [
                'encoded' => 'a&eplus;b',
                'decoded' => 'a⩱b',
            ],
            358 => [
                'encoded' => 'a&epsi;b',
                'decoded' => 'aεb',
            ],
            359 => [
                'encoded' => 'a&Epsilon;b',
                'decoded' => 'aΕb',
            ],
            360 => [
                'encoded' => 'a&epsiv;b',
                'decoded' => 'aϵb',
            ],
            361 => [
                'encoded' => 'a&Equal;b',
                'decoded' => 'a⩵b',
            ],
            362 => [
                'encoded' => 'a&equiv;b',
                'decoded' => 'a≡b',
            ],
            363 => [
                'encoded' => 'a&equivDD;b',
                'decoded' => 'a⩸b',
            ],
            364 => [
                'encoded' => 'a&eqvparsl;b',
                'decoded' => 'a⧥b',
            ],
            365 => [
                'encoded' => 'a&erarr;b',
                'decoded' => 'a⥱b',
            ],
            366 => [
                'encoded' => 'a&erDot;b',
                'decoded' => 'a≓b',
            ],
            367 => [
                'encoded' => 'a&escr;b',
                'decoded' => 'aℯb',
            ],
            368 => [
                'encoded' => 'a&Escr;b',
                'decoded' => 'aℰb',
            ],
            369 => [
                'encoded' => 'a&Esim;b',
                'decoded' => 'a⩳b',
            ],
            370 => [
                'encoded' => 'a&esim;b',
                'decoded' => 'a≂b',
            ],
            371 => [
                'encoded' => 'a&Eta;b',
                'decoded' => 'aΗb',
            ],
            372 => [
                'encoded' => 'a&eta;b',
                'decoded' => 'aηb',
            ],
            373 => [
                'encoded' => 'a&ETH;b',
                'decoded' => 'aÐb',
            ],
            374 => [
                'encoded' => 'a&eth;b',
                'decoded' => 'aðb',
            ],
            375 => [
                'encoded' => 'a&Euml;b',
                'decoded' => 'aËb',
            ],
            376 => [
                'encoded' => 'a&euml;b',
                'decoded' => 'aëb',
            ],
            377 => [
                'encoded' => 'a&euro;b',
                'decoded' => 'a€b',
            ],
            378 => [
                'encoded' => 'a&exist;b',
                'decoded' => 'a∃b',
            ],
            379 => [
                'encoded' => 'a&Fcy;b',
                'decoded' => 'aФb',
            ],
            380 => [
                'encoded' => 'a&fcy;b',
                'decoded' => 'aфb',
            ],
            381 => [
                'encoded' => 'a&female;b',
                'decoded' => 'a♀b',
            ],
            382 => [
                'encoded' => 'a&ffilig;b',
                'decoded' => 'aﬃb',
            ],
            383 => [
                'encoded' => 'a&fflig;b',
                'decoded' => 'aﬀb',
            ],
            384 => [
                'encoded' => 'a&ffllig;b',
                'decoded' => 'aﬄb',
            ],
            385 => [
                'encoded' => 'a&Ffr;b',
                'decoded' => 'a𝔉b',
            ],
            386 => [
                'encoded' => 'a&ffr;b',
                'decoded' => 'a𝔣b',
            ],
            387 => [
                'encoded' => 'a&filig;b',
                'decoded' => 'aﬁb',
            ],
            388 => [
                'encoded' => 'a&FilledSmallSquare;b',
                'decoded' => 'a◼b',
            ],
            389 => [
                'encoded' => 'a&flat;b',
                'decoded' => 'a♭b',
            ],
            390 => [
                'encoded' => 'a&fllig;b',
                'decoded' => 'aﬂb',
            ],
            391 => [
                'encoded' => 'a&fltns;b',
                'decoded' => 'a▱b',
            ],
            392 => [
                'encoded' => 'a&fnof;b',
                'decoded' => 'aƒb',
            ],
            393 => [
                'encoded' => 'a&Fopf;b',
                'decoded' => 'a𝔽b',
            ],
            394 => [
                'encoded' => 'a&fopf;b',
                'decoded' => 'a𝕗b',
            ],
            395 => [
                'encoded' => 'a&forall;b',
                'decoded' => 'a∀b',
            ],
            396 => [
                'encoded' => 'a&fork;b',
                'decoded' => 'a⋔b',
            ],
            397 => [
                'encoded' => 'a&forkv;b',
                'decoded' => 'a⫙b',
            ],
            398 => [
                'encoded' => 'a&fpartint;b',
                'decoded' => 'a⨍b',
            ],
            399 => [
                'encoded' => 'a&frac13;b',
                'decoded' => 'a⅓b',
            ],
            400 => [
                'encoded' => 'a&frac14;b',
                'decoded' => 'a¼b',
            ],
            401 => [
                'encoded' => 'a&frac15;b',
                'decoded' => 'a⅕b',
            ],
            402 => [
                'encoded' => 'a&frac16;b',
                'decoded' => 'a⅙b',
            ],
            403 => [
                'encoded' => 'a&frac18;b',
                'decoded' => 'a⅛b',
            ],
            404 => [
                'encoded' => 'a&frac23;b',
                'decoded' => 'a⅔b',
            ],
            405 => [
                'encoded' => 'a&frac25;b',
                'decoded' => 'a⅖b',
            ],
            406 => [
                'encoded' => 'a&frac34;b',
                'decoded' => 'a¾b',
            ],
            407 => [
                'encoded' => 'a&frac35;b',
                'decoded' => 'a⅗b',
            ],
            408 => [
                'encoded' => 'a&frac38;b',
                'decoded' => 'a⅜b',
            ],
            409 => [
                'encoded' => 'a&frac45;b',
                'decoded' => 'a⅘b',
            ],
            410 => [
                'encoded' => 'a&frac56;b',
                'decoded' => 'a⅚b',
            ],
            411 => [
                'encoded' => 'a&frac58;b',
                'decoded' => 'a⅝b',
            ],
            412 => [
                'encoded' => 'a&frac78;b',
                'decoded' => 'a⅞b',
            ],
            413 => [
                'encoded' => 'a&frasl;b',
                'decoded' => 'a⁄b',
            ],
            414 => [
                'encoded' => 'a&frown;b',
                'decoded' => 'a⌢b',
            ],
            415 => [
                'encoded' => 'a&fscr;b',
                'decoded' => 'a𝒻b',
            ],
            416 => [
                'encoded' => 'a&Fscr;b',
                'decoded' => 'aℱb',
            ],
            417 => [
                'encoded' => 'a&gacute;b',
                'decoded' => 'aǵb',
            ],
            418 => [
                'encoded' => 'a&Gamma;b',
                'decoded' => 'aΓb',
            ],
            419 => [
                'encoded' => 'a&gamma;b',
                'decoded' => 'aγb',
            ],
            420 => [
                'encoded' => 'a&Gammad;b',
                'decoded' => 'aϜb',
            ],
            421 => [
                'encoded' => 'a&gammad;b',
                'decoded' => 'aϝb',
            ],
            422 => [
                'encoded' => 'a&gap;b',
                'decoded' => 'a⪆b',
            ],
            423 => [
                'encoded' => 'a&Gbreve;b',
                'decoded' => 'aĞb',
            ],
            424 => [
                'encoded' => 'a&gbreve;b',
                'decoded' => 'ağb',
            ],
            425 => [
                'encoded' => 'a&Gcedil;b',
                'decoded' => 'aĢb',
            ],
            426 => [
                'encoded' => 'a&Gcirc;b',
                'decoded' => 'aĜb',
            ],
            427 => [
                'encoded' => 'a&gcirc;b',
                'decoded' => 'aĝb',
            ],
            428 => [
                'encoded' => 'a&Gcy;b',
                'decoded' => 'aГb',
            ],
            429 => [
                'encoded' => 'a&gcy;b',
                'decoded' => 'aгb',
            ],
            430 => [
                'encoded' => 'a&Gdot;b',
                'decoded' => 'aĠb',
            ],
            431 => [
                'encoded' => 'a&gdot;b',
                'decoded' => 'aġb',
            ],
            432 => [
                'encoded' => 'a&ge;b',
                'decoded' => 'a≥b',
            ],
            433 => [
                'encoded' => 'a&gE;b',
                'decoded' => 'a≧b',
            ],
            434 => [
                'encoded' => 'a&gEl;b',
                'decoded' => 'a⪌b',
            ],
            435 => [
                'encoded' => 'a&gel;b',
                'decoded' => 'a⋛b',
            ],
            436 => [
                'encoded' => 'a&gescc;b',
                'decoded' => 'a⪩b',
            ],
            437 => [
                'encoded' => 'a&ges;b',
                'decoded' => 'a⩾b',
            ],
            438 => [
                'encoded' => 'a&gesdot;b',
                'decoded' => 'a⪀b',
            ],
            439 => [
                'encoded' => 'a&gesdoto;b',
                'decoded' => 'a⪂b',
            ],
            440 => [
                'encoded' => 'a&gesdotol;b',
                'decoded' => 'a⪄b',
            ],
            441 => [
                'encoded' => 'a&gesl;b',
                'decoded' => 'a⋛︀b',
            ],
            442 => [
                'encoded' => 'a&gesles;b',
                'decoded' => 'a⪔b',
            ],
            443 => [
                'encoded' => 'a&Gfr;b',
                'decoded' => 'a𝔊b',
            ],
            444 => [
                'encoded' => 'a&gfr;b',
                'decoded' => 'a𝔤b',
            ],
            445 => [
                'encoded' => 'a&gg;b',
                'decoded' => 'a≫b',
            ],
            446 => [
                'encoded' => 'a&Gg;b',
                'decoded' => 'a⋙b',
            ],
            447 => [
                'encoded' => 'a&gimel;b',
                'decoded' => 'aℷb',
            ],
            448 => [
                'encoded' => 'a&GJcy;b',
                'decoded' => 'aЃb',
            ],
            449 => [
                'encoded' => 'a&gjcy;b',
                'decoded' => 'aѓb',
            ],
            450 => [
                'encoded' => 'a&gla;b',
                'decoded' => 'a⪥b',
            ],
            451 => [
                'encoded' => 'a&gl;b',
                'decoded' => 'a≷b',
            ],
            452 => [
                'encoded' => 'a&glE;b',
                'decoded' => 'a⪒b',
            ],
            453 => [
                'encoded' => 'a&glj;b',
                'decoded' => 'a⪤b',
            ],
            454 => [
                'encoded' => 'a&gnap;b',
                'decoded' => 'a⪊b',
            ],
            455 => [
                'encoded' => 'a&gne;b',
                'decoded' => 'a⪈b',
            ],
            456 => [
                'encoded' => 'a&gnE;b',
                'decoded' => 'a≩b',
            ],
            457 => [
                'encoded' => 'a&gnsim;b',
                'decoded' => 'a⋧b',
            ],
            458 => [
                'encoded' => 'a&Gopf;b',
                'decoded' => 'a𝔾b',
            ],
            459 => [
                'encoded' => 'a&gopf;b',
                'decoded' => 'a𝕘b',
            ],
            460 => [
                'encoded' => 'a&GreaterGreater;b',
                'decoded' => 'a⪢b',
            ],
            461 => [
                'encoded' => 'a&Gscr;b',
                'decoded' => 'a𝒢b',
            ],
            462 => [
                'encoded' => 'a&gscr;b',
                'decoded' => 'aℊb',
            ],
            463 => [
                'encoded' => 'a&gsim;b',
                'decoded' => 'a≳b',
            ],
            464 => [
                'encoded' => 'a&gsime;b',
                'decoded' => 'a⪎b',
            ],
            465 => [
                'encoded' => 'a&gsiml;b',
                'decoded' => 'a⪐b',
            ],
            466 => [
                'encoded' => 'a&gtcc;b',
                'decoded' => 'a⪧b',
            ],
            467 => [
                'encoded' => 'a&gtcir;b',
                'decoded' => 'a⩺b',
            ],
            468 => [
                'encoded' => 'a&gt;b',
                'decoded' => 'a>b',
            ],
            469 => [
                'encoded' => 'a&gtdot;b',
                'decoded' => 'a⋗b',
            ],
            470 => [
                'encoded' => 'a&gtlPar;b',
                'decoded' => 'a⦕b',
            ],
            471 => [
                'encoded' => 'a&gtquest;b',
                'decoded' => 'a⩼b',
            ],
            472 => [
                'encoded' => 'a&gtrarr;b',
                'decoded' => 'a⥸b',
            ],
            473 => [
                'encoded' => 'a&gvnE;b',
                'decoded' => 'a≩︀b',
            ],
            474 => [
                'encoded' => 'a&hairsp;b',
                'decoded' => 'a b',
            ],
            475 => [
                'encoded' => 'a&half;b',
                'decoded' => 'a½b',
            ],
            476 => [
                'encoded' => 'a&HARDcy;b',
                'decoded' => 'aЪb',
            ],
            477 => [
                'encoded' => 'a&hardcy;b',
                'decoded' => 'aъb',
            ],
            478 => [
                'encoded' => 'a&harrcir;b',
                'decoded' => 'a⥈b',
            ],
            479 => [
                'encoded' => 'a&harr;b',
                'decoded' => 'a↔b',
            ],
            480 => [
                'encoded' => 'a&harrw;b',
                'decoded' => 'a↭b',
            ],
            481 => [
                'encoded' => 'a&hbar;b',
                'decoded' => 'aℏb',
            ],
            482 => [
                'encoded' => 'a&Hcirc;b',
                'decoded' => 'aĤb',
            ],
            483 => [
                'encoded' => 'a&hcirc;b',
                'decoded' => 'aĥb',
            ],
            484 => [
                'encoded' => 'a&hearts;b',
                'decoded' => 'a♥b',
            ],
            485 => [
                'encoded' => 'a&hercon;b',
                'decoded' => 'a⊹b',
            ],
            486 => [
                'encoded' => 'a&hfr;b',
                'decoded' => 'a𝔥b',
            ],
            487 => [
                'encoded' => 'a&Hfr;b',
                'decoded' => 'aℌb',
            ],
            488 => [
                'encoded' => 'a&hoarr;b',
                'decoded' => 'a⇿b',
            ],
            489 => [
                'encoded' => 'a&homtht;b',
                'decoded' => 'a∻b',
            ],
            490 => [
                'encoded' => 'a&hopf;b',
                'decoded' => 'a𝕙b',
            ],
            491 => [
                'encoded' => 'a&Hopf;b',
                'decoded' => 'aℍb',
            ],
            492 => [
                'encoded' => 'a&horbar;b',
                'decoded' => 'a―b',
            ],
            493 => [
                'encoded' => 'a&hscr;b',
                'decoded' => 'a𝒽b',
            ],
            494 => [
                'encoded' => 'a&Hscr;b',
                'decoded' => 'aℋb',
            ],
            495 => [
                'encoded' => 'a&Hstrok;b',
                'decoded' => 'aĦb',
            ],
            496 => [
                'encoded' => 'a&hstrok;b',
                'decoded' => 'aħb',
            ],
            497 => [
                'encoded' => 'a&hybull;b',
                'decoded' => 'a⁃b',
            ],
            498 => [
                'encoded' => 'a&Iacute;b',
                'decoded' => 'aÍb',
            ],
            499 => [
                'encoded' => 'a&iacute;b',
                'decoded' => 'aíb',
            ],
            500 => [
                'encoded' => 'a&ic;b',
                'decoded' => 'a⁣b',
            ],
            501 => [
                'encoded' => 'a&Icirc;b',
                'decoded' => 'aÎb',
            ],
            502 => [
                'encoded' => 'a&icirc;b',
                'decoded' => 'aîb',
            ],
            503 => [
                'encoded' => 'a&Icy;b',
                'decoded' => 'aИb',
            ],
            504 => [
                'encoded' => 'a&icy;b',
                'decoded' => 'aиb',
            ],
            505 => [
                'encoded' => 'a&Idot;b',
                'decoded' => 'aİb',
            ],
            506 => [
                'encoded' => 'a&IEcy;b',
                'decoded' => 'aЕb',
            ],
            507 => [
                'encoded' => 'a&iecy;b',
                'decoded' => 'aеb',
            ],
            508 => [
                'encoded' => 'a&iexcl;b',
                'decoded' => 'a¡b',
            ],
            509 => [
                'encoded' => 'a&iff;b',
                'decoded' => 'a⇔b',
            ],
            510 => [
                'encoded' => 'a&ifr;b',
                'decoded' => 'a𝔦b',
            ],
            511 => [
                'encoded' => 'a&Igrave;b',
                'decoded' => 'aÌb',
            ],
            512 => [
                'encoded' => 'a&igrave;b',
                'decoded' => 'aìb',
            ],
            513 => [
                'encoded' => 'a&ii;b',
                'decoded' => 'aⅈb',
            ],
            514 => [
                'encoded' => 'a&iinfin;b',
                'decoded' => 'a⧜b',
            ],
            515 => [
                'encoded' => 'a&iiota;b',
                'decoded' => 'a℩b',
            ],
            516 => [
                'encoded' => 'a&IJlig;b',
                'decoded' => 'aĲb',
            ],
            517 => [
                'encoded' => 'a&ijlig;b',
                'decoded' => 'aĳb',
            ],
            518 => [
                'encoded' => 'a&Imacr;b',
                'decoded' => 'aĪb',
            ],
            519 => [
                'encoded' => 'a&imacr;b',
                'decoded' => 'aīb',
            ],
            520 => [
                'encoded' => 'a&imath;b',
                'decoded' => 'aıb',
            ],
            521 => [
                'encoded' => 'a&Im;b',
                'decoded' => 'aℑb',
            ],
            522 => [
                'encoded' => 'a&imof;b',
                'decoded' => 'a⊷b',
            ],
            523 => [
                'encoded' => 'a&imped;b',
                'decoded' => 'aƵb',
            ],
            524 => [
                'encoded' => 'a&incare;b',
                'decoded' => 'a℅b',
            ],
            525 => [
                'encoded' => 'a&in;b',
                'decoded' => 'a∈b',
            ],
            526 => [
                'encoded' => 'a&infin;b',
                'decoded' => 'a∞b',
            ],
            527 => [
                'encoded' => 'a&infintie;b',
                'decoded' => 'a⧝b',
            ],
            528 => [
                'encoded' => 'a&intcal;b',
                'decoded' => 'a⊺b',
            ],
            529 => [
                'encoded' => 'a&int;b',
                'decoded' => 'a∫b',
            ],
            530 => [
                'encoded' => 'a&Int;b',
                'decoded' => 'a∬b',
            ],
            531 => [
                'encoded' => 'a&intlarhk;b',
                'decoded' => 'a⨗b',
            ],
            532 => [
                'encoded' => 'a&IOcy;b',
                'decoded' => 'aЁb',
            ],
            533 => [
                'encoded' => 'a&iocy;b',
                'decoded' => 'aёb',
            ],
            534 => [
                'encoded' => 'a&Iogon;b',
                'decoded' => 'aĮb',
            ],
            535 => [
                'encoded' => 'a&iogon;b',
                'decoded' => 'aįb',
            ],
            536 => [
                'encoded' => 'a&Iopf;b',
                'decoded' => 'a𝕀b',
            ],
            537 => [
                'encoded' => 'a&iopf;b',
                'decoded' => 'a𝕚b',
            ],
            538 => [
                'encoded' => 'a&Iota;b',
                'decoded' => 'aΙb',
            ],
            539 => [
                'encoded' => 'a&iota;b',
                'decoded' => 'aιb',
            ],
            540 => [
                'encoded' => 'a&iprod;b',
                'decoded' => 'a⨼b',
            ],
            541 => [
                'encoded' => 'a&iquest;b',
                'decoded' => 'a¿b',
            ],
            542 => [
                'encoded' => 'a&iscr;b',
                'decoded' => 'a𝒾b',
            ],
            543 => [
                'encoded' => 'a&Iscr;b',
                'decoded' => 'aℐb',
            ],
            544 => [
                'encoded' => 'a&isindot;b',
                'decoded' => 'a⋵b',
            ],
            545 => [
                'encoded' => 'a&isinE;b',
                'decoded' => 'a⋹b',
            ],
            546 => [
                'encoded' => 'a&isins;b',
                'decoded' => 'a⋴b',
            ],
            547 => [
                'encoded' => 'a&isinsv;b',
                'decoded' => 'a⋳b',
            ],
            548 => [
                'encoded' => 'a&it;b',
                'decoded' => 'a⁢b',
            ],
            549 => [
                'encoded' => 'a&Itilde;b',
                'decoded' => 'aĨb',
            ],
            550 => [
                'encoded' => 'a&itilde;b',
                'decoded' => 'aĩb',
            ],
            551 => [
                'encoded' => 'a&Iukcy;b',
                'decoded' => 'aІb',
            ],
            552 => [
                'encoded' => 'a&iukcy;b',
                'decoded' => 'aіb',
            ],
            553 => [
                'encoded' => 'a&Iuml;b',
                'decoded' => 'aÏb',
            ],
            554 => [
                'encoded' => 'a&iuml;b',
                'decoded' => 'aïb',
            ],
            555 => [
                'encoded' => 'a&Jcirc;b',
                'decoded' => 'aĴb',
            ],
            556 => [
                'encoded' => 'a&jcirc;b',
                'decoded' => 'aĵb',
            ],
            557 => [
                'encoded' => 'a&Jcy;b',
                'decoded' => 'aЙb',
            ],
            558 => [
                'encoded' => 'a&jcy;b',
                'decoded' => 'aйb',
            ],
            559 => [
                'encoded' => 'a&Jfr;b',
                'decoded' => 'a𝔍b',
            ],
            560 => [
                'encoded' => 'a&jfr;b',
                'decoded' => 'a𝔧b',
            ],
            561 => [
                'encoded' => 'a&jmath;b',
                'decoded' => 'aȷb',
            ],
            562 => [
                'encoded' => 'a&Jopf;b',
                'decoded' => 'a𝕁b',
            ],
            563 => [
                'encoded' => 'a&jopf;b',
                'decoded' => 'a𝕛b',
            ],
            564 => [
                'encoded' => 'a&Jscr;b',
                'decoded' => 'a𝒥b',
            ],
            565 => [
                'encoded' => 'a&jscr;b',
                'decoded' => 'a𝒿b',
            ],
            566 => [
                'encoded' => 'a&Jsercy;b',
                'decoded' => 'aЈb',
            ],
            567 => [
                'encoded' => 'a&jsercy;b',
                'decoded' => 'aјb',
            ],
            568 => [
                'encoded' => 'a&Jukcy;b',
                'decoded' => 'aЄb',
            ],
            569 => [
                'encoded' => 'a&jukcy;b',
                'decoded' => 'aєb',
            ],
            570 => [
                'encoded' => 'a&Kappa;b',
                'decoded' => 'aΚb',
            ],
            571 => [
                'encoded' => 'a&kappa;b',
                'decoded' => 'aκb',
            ],
            572 => [
                'encoded' => 'a&kappav;b',
                'decoded' => 'aϰb',
            ],
            573 => [
                'encoded' => 'a&Kcedil;b',
                'decoded' => 'aĶb',
            ],
            574 => [
                'encoded' => 'a&kcedil;b',
                'decoded' => 'aķb',
            ],
            575 => [
                'encoded' => 'a&Kcy;b',
                'decoded' => 'aКb',
            ],
            576 => [
                'encoded' => 'a&kcy;b',
                'decoded' => 'aкb',
            ],
            577 => [
                'encoded' => 'a&Kfr;b',
                'decoded' => 'a𝔎b',
            ],
            578 => [
                'encoded' => 'a&kfr;b',
                'decoded' => 'a𝔨b',
            ],
            579 => [
                'encoded' => 'a&kgreen;b',
                'decoded' => 'aĸb',
            ],
            580 => [
                'encoded' => 'a&KHcy;b',
                'decoded' => 'aХb',
            ],
            581 => [
                'encoded' => 'a&khcy;b',
                'decoded' => 'aхb',
            ],
            582 => [
                'encoded' => 'a&KJcy;b',
                'decoded' => 'aЌb',
            ],
            583 => [
                'encoded' => 'a&kjcy;b',
                'decoded' => 'aќb',
            ],
            584 => [
                'encoded' => 'a&Kopf;b',
                'decoded' => 'a𝕂b',
            ],
            585 => [
                'encoded' => 'a&kopf;b',
                'decoded' => 'a𝕜b',
            ],
            586 => [
                'encoded' => 'a&Kscr;b',
                'decoded' => 'a𝒦b',
            ],
            587 => [
                'encoded' => 'a&kscr;b',
                'decoded' => 'a𝓀b',
            ],
            588 => [
                'encoded' => 'a&lAarr;b',
                'decoded' => 'a⇚b',
            ],
            589 => [
                'encoded' => 'a&Lacute;b',
                'decoded' => 'aĹb',
            ],
            590 => [
                'encoded' => 'a&lacute;b',
                'decoded' => 'aĺb',
            ],
            591 => [
                'encoded' => 'a&laemptyv;b',
                'decoded' => 'a⦴b',
            ],
            592 => [
                'encoded' => 'a&Lambda;b',
                'decoded' => 'aΛb',
            ],
            593 => [
                'encoded' => 'a&lambda;b',
                'decoded' => 'aλb',
            ],
            594 => [
                'encoded' => 'a&lang;b',
                'decoded' => 'a⟨b',
            ],
            595 => [
                'encoded' => 'a&Lang;b',
                'decoded' => 'a⟪b',
            ],
            596 => [
                'encoded' => 'a&langd;b',
                'decoded' => 'a⦑b',
            ],
            597 => [
                'encoded' => 'a&lap;b',
                'decoded' => 'a⪅b',
            ],
            598 => [
                'encoded' => 'a&laquo;b',
                'decoded' => 'a«b',
            ],
            599 => [
                'encoded' => 'a&larrb;b',
                'decoded' => 'a⇤b',
            ],
            600 => [
                'encoded' => 'a&larrbfs;b',
                'decoded' => 'a⤟b',
            ],
            601 => [
                'encoded' => 'a&larr;b',
                'decoded' => 'a←b',
            ],
            602 => [
                'encoded' => 'a&Larr;b',
                'decoded' => 'a↞b',
            ],
            603 => [
                'encoded' => 'a&lArr;b',
                'decoded' => 'a⇐b',
            ],
            604 => [
                'encoded' => 'a&larrfs;b',
                'decoded' => 'a⤝b',
            ],
            605 => [
                'encoded' => 'a&larrhk;b',
                'decoded' => 'a↩b',
            ],
            606 => [
                'encoded' => 'a&larrlp;b',
                'decoded' => 'a↫b',
            ],
            607 => [
                'encoded' => 'a&larrpl;b',
                'decoded' => 'a⤹b',
            ],
            608 => [
                'encoded' => 'a&larrsim;b',
                'decoded' => 'a⥳b',
            ],
            609 => [
                'encoded' => 'a&larrtl;b',
                'decoded' => 'a↢b',
            ],
            610 => [
                'encoded' => 'a&latail;b',
                'decoded' => 'a⤙b',
            ],
            611 => [
                'encoded' => 'a&lAtail;b',
                'decoded' => 'a⤛b',
            ],
            612 => [
                'encoded' => 'a&lat;b',
                'decoded' => 'a⪫b',
            ],
            613 => [
                'encoded' => 'a&late;b',
                'decoded' => 'a⪭b',
            ],
            614 => [
                'encoded' => 'a&lates;b',
                'decoded' => 'a⪭︀b',
            ],
            615 => [
                'encoded' => 'a&lbarr;b',
                'decoded' => 'a⤌b',
            ],
            616 => [
                'encoded' => 'a&lBarr;b',
                'decoded' => 'a⤎b',
            ],
            617 => [
                'encoded' => 'a&lbbrk;b',
                'decoded' => 'a❲b',
            ],
            618 => [
                'encoded' => 'a&lbrke;b',
                'decoded' => 'a⦋b',
            ],
            619 => [
                'encoded' => 'a&lbrksld;b',
                'decoded' => 'a⦏b',
            ],
            620 => [
                'encoded' => 'a&lbrkslu;b',
                'decoded' => 'a⦍b',
            ],
            621 => [
                'encoded' => 'a&Lcaron;b',
                'decoded' => 'aĽb',
            ],
            622 => [
                'encoded' => 'a&lcaron;b',
                'decoded' => 'aľb',
            ],
            623 => [
                'encoded' => 'a&Lcedil;b',
                'decoded' => 'aĻb',
            ],
            624 => [
                'encoded' => 'a&lcedil;b',
                'decoded' => 'aļb',
            ],
            625 => [
                'encoded' => 'a&lceil;b',
                'decoded' => 'a⌈b',
            ],
            626 => [
                'encoded' => 'a&Lcy;b',
                'decoded' => 'aЛb',
            ],
            627 => [
                'encoded' => 'a&lcy;b',
                'decoded' => 'aлb',
            ],
            628 => [
                'encoded' => 'a&ldca;b',
                'decoded' => 'a⤶b',
            ],
            629 => [
                'encoded' => 'a&ldquo;b',
                'decoded' => 'a“b',
            ],
            630 => [
                'encoded' => 'a&ldrdhar;b',
                'decoded' => 'a⥧b',
            ],
            631 => [
                'encoded' => 'a&ldrushar;b',
                'decoded' => 'a⥋b',
            ],
            632 => [
                'encoded' => 'a&ldsh;b',
                'decoded' => 'a↲b',
            ],
            633 => [
                'encoded' => 'a&le;b',
                'decoded' => 'a≤b',
            ],
            634 => [
                'encoded' => 'a&lE;b',
                'decoded' => 'a≦b',
            ],
            635 => [
                'encoded' => 'a&LeftDownTeeVector;b',
                'decoded' => 'a⥡b',
            ],
            636 => [
                'encoded' => 'a&LeftDownVectorBar;b',
                'decoded' => 'a⥙b',
            ],
            637 => [
                'encoded' => 'a&LeftRightVector;b',
                'decoded' => 'a⥎b',
            ],
            638 => [
                'encoded' => 'a&LeftTeeVector;b',
                'decoded' => 'a⥚b',
            ],
            639 => [
                'encoded' => 'a&LeftTriangleBar;b',
                'decoded' => 'a⧏b',
            ],
            640 => [
                'encoded' => 'a&LeftUpDownVector;b',
                'decoded' => 'a⥑b',
            ],
            641 => [
                'encoded' => 'a&LeftUpTeeVector;b',
                'decoded' => 'a⥠b',
            ],
            642 => [
                'encoded' => 'a&LeftUpVectorBar;b',
                'decoded' => 'a⥘b',
            ],
            643 => [
                'encoded' => 'a&LeftVectorBar;b',
                'decoded' => 'a⥒b',
            ],
            644 => [
                'encoded' => 'a&lEg;b',
                'decoded' => 'a⪋b',
            ],
            645 => [
                'encoded' => 'a&leg;b',
                'decoded' => 'a⋚b',
            ],
            646 => [
                'encoded' => 'a&lescc;b',
                'decoded' => 'a⪨b',
            ],
            647 => [
                'encoded' => 'a&les;b',
                'decoded' => 'a⩽b',
            ],
            648 => [
                'encoded' => 'a&lesdot;b',
                'decoded' => 'a⩿b',
            ],
            649 => [
                'encoded' => 'a&lesdoto;b',
                'decoded' => 'a⪁b',
            ],
            650 => [
                'encoded' => 'a&lesdotor;b',
                'decoded' => 'a⪃b',
            ],
            651 => [
                'encoded' => 'a&lesg;b',
                'decoded' => 'a⋚︀b',
            ],
            652 => [
                'encoded' => 'a&lesges;b',
                'decoded' => 'a⪓b',
            ],
            653 => [
                'encoded' => 'a&LessLess;b',
                'decoded' => 'a⪡b',
            ],
            654 => [
                'encoded' => 'a&lfisht;b',
                'decoded' => 'a⥼b',
            ],
            655 => [
                'encoded' => 'a&lfloor;b',
                'decoded' => 'a⌊b',
            ],
            656 => [
                'encoded' => 'a&Lfr;b',
                'decoded' => 'a𝔏b',
            ],
            657 => [
                'encoded' => 'a&lfr;b',
                'decoded' => 'a𝔩b',
            ],
            658 => [
                'encoded' => 'a&lg;b',
                'decoded' => 'a≶b',
            ],
            659 => [
                'encoded' => 'a&lgE;b',
                'decoded' => 'a⪑b',
            ],
            660 => [
                'encoded' => 'a&lHar;b',
                'decoded' => 'a⥢b',
            ],
            661 => [
                'encoded' => 'a&lhard;b',
                'decoded' => 'a↽b',
            ],
            662 => [
                'encoded' => 'a&lharu;b',
                'decoded' => 'a↼b',
            ],
            663 => [
                'encoded' => 'a&lharul;b',
                'decoded' => 'a⥪b',
            ],
            664 => [
                'encoded' => 'a&lhblk;b',
                'decoded' => 'a▄b',
            ],
            665 => [
                'encoded' => 'a&LJcy;b',
                'decoded' => 'aЉb',
            ],
            666 => [
                'encoded' => 'a&ljcy;b',
                'decoded' => 'aљb',
            ],
            667 => [
                'encoded' => 'a&llarr;b',
                'decoded' => 'a⇇b',
            ],
            668 => [
                'encoded' => 'a&ll;b',
                'decoded' => 'a≪b',
            ],
            669 => [
                'encoded' => 'a&Ll;b',
                'decoded' => 'a⋘b',
            ],
            670 => [
                'encoded' => 'a&llhard;b',
                'decoded' => 'a⥫b',
            ],
            671 => [
                'encoded' => 'a&lltri;b',
                'decoded' => 'a◺b',
            ],
            672 => [
                'encoded' => 'a&Lmidot;b',
                'decoded' => 'aĿb',
            ],
            673 => [
                'encoded' => 'a&lmidot;b',
                'decoded' => 'aŀb',
            ],
            674 => [
                'encoded' => 'a&lmoust;b',
                'decoded' => 'a⎰b',
            ],
            675 => [
                'encoded' => 'a&lnap;b',
                'decoded' => 'a⪉b',
            ],
            676 => [
                'encoded' => 'a&lne;b',
                'decoded' => 'a⪇b',
            ],
            677 => [
                'encoded' => 'a&lnE;b',
                'decoded' => 'a≨b',
            ],
            678 => [
                'encoded' => 'a&lnsim;b',
                'decoded' => 'a⋦b',
            ],
            679 => [
                'encoded' => 'a&loang;b',
                'decoded' => 'a⟬b',
            ],
            680 => [
                'encoded' => 'a&loarr;b',
                'decoded' => 'a⇽b',
            ],
            681 => [
                'encoded' => 'a&lobrk;b',
                'decoded' => 'a⟦b',
            ],
            682 => [
                'encoded' => 'a&lopar;b',
                'decoded' => 'a⦅b',
            ],
            683 => [
                'encoded' => 'a&Lopf;b',
                'decoded' => 'a𝕃b',
            ],
            684 => [
                'encoded' => 'a&lopf;b',
                'decoded' => 'a𝕝b',
            ],
            685 => [
                'encoded' => 'a&loplus;b',
                'decoded' => 'a⨭b',
            ],
            686 => [
                'encoded' => 'a&lotimes;b',
                'decoded' => 'a⨴b',
            ],
            687 => [
                'encoded' => 'a&lowast;b',
                'decoded' => 'a∗b',
            ],
            688 => [
                'encoded' => 'a&loz;b',
                'decoded' => 'a◊b',
            ],
            689 => [
                'encoded' => 'a&lozf;b',
                'decoded' => 'a⧫b',
            ],
            690 => [
                'encoded' => 'a&lparlt;b',
                'decoded' => 'a⦓b',
            ],
            691 => [
                'encoded' => 'a&lrarr;b',
                'decoded' => 'a⇆b',
            ],
            692 => [
                'encoded' => 'a&lrhar;b',
                'decoded' => 'a⇋b',
            ],
            693 => [
                'encoded' => 'a&lrhard;b',
                'decoded' => 'a⥭b',
            ],
            694 => [
                'encoded' => 'a&lrm;b',
                'decoded' => 'a‎b',
            ],
            695 => [
                'encoded' => 'a&lrtri;b',
                'decoded' => 'a⊿b',
            ],
            696 => [
                'encoded' => 'a&lsaquo;b',
                'decoded' => 'a‹b',
            ],
            697 => [
                'encoded' => 'a&lscr;b',
                'decoded' => 'a𝓁b',
            ],
            698 => [
                'encoded' => 'a&Lscr;b',
                'decoded' => 'aℒb',
            ],
            699 => [
                'encoded' => 'a&lsh;b',
                'decoded' => 'a↰b',
            ],
            700 => [
                'encoded' => 'a&lsim;b',
                'decoded' => 'a≲b',
            ],
            701 => [
                'encoded' => 'a&lsime;b',
                'decoded' => 'a⪍b',
            ],
            702 => [
                'encoded' => 'a&lsimg;b',
                'decoded' => 'a⪏b',
            ],
            703 => [
                'encoded' => 'a&lsquo;b',
                'decoded' => 'a‘b',
            ],
            704 => [
                'encoded' => 'a&Lstrok;b',
                'decoded' => 'aŁb',
            ],
            705 => [
                'encoded' => 'a&lstrok;b',
                'decoded' => 'ałb',
            ],
            706 => [
                'encoded' => 'a&ltcc;b',
                'decoded' => 'a⪦b',
            ],
            707 => [
                'encoded' => 'a&ltcir;b',
                'decoded' => 'a⩹b',
            ],
            708 => [
                'encoded' => 'a&lt;b',
                'decoded' => 'a<b',
            ],
            709 => [
                'encoded' => 'a&ltdot;b',
                'decoded' => 'a⋖b',
            ],
            710 => [
                'encoded' => 'a&lthree;b',
                'decoded' => 'a⋋b',
            ],
            711 => [
                'encoded' => 'a&ltimes;b',
                'decoded' => 'a⋉b',
            ],
            712 => [
                'encoded' => 'a&ltlarr;b',
                'decoded' => 'a⥶b',
            ],
            713 => [
                'encoded' => 'a&ltquest;b',
                'decoded' => 'a⩻b',
            ],
            714 => [
                'encoded' => 'a&ltri;b',
                'decoded' => 'a◃b',
            ],
            715 => [
                'encoded' => 'a&ltrie;b',
                'decoded' => 'a⊴b',
            ],
            716 => [
                'encoded' => 'a&ltrif;b',
                'decoded' => 'a◂b',
            ],
            717 => [
                'encoded' => 'a&ltrPar;b',
                'decoded' => 'a⦖b',
            ],
            718 => [
                'encoded' => 'a&lurdshar;b',
                'decoded' => 'a⥊b',
            ],
            719 => [
                'encoded' => 'a&luruhar;b',
                'decoded' => 'a⥦b',
            ],
            720 => [
                'encoded' => 'a&lvnE;b',
                'decoded' => 'a≨︀b',
            ],
            721 => [
                'encoded' => 'a&macr;b',
                'decoded' => 'a¯b',
            ],
            722 => [
                'encoded' => 'a&male;b',
                'decoded' => 'a♂b',
            ],
            723 => [
                'encoded' => 'a&malt;b',
                'decoded' => 'a✠b',
            ],
            724 => [
                'encoded' => 'a&Map;b',
                'decoded' => 'a⤅b',
            ],
            725 => [
                'encoded' => 'a&map;b',
                'decoded' => 'a↦b',
            ],
            726 => [
                'encoded' => 'a&mapstodown;b',
                'decoded' => 'a↧b',
            ],
            727 => [
                'encoded' => 'a&mapstoleft;b',
                'decoded' => 'a↤b',
            ],
            728 => [
                'encoded' => 'a&mapstoup;b',
                'decoded' => 'a↥b',
            ],
            729 => [
                'encoded' => 'a&marker;b',
                'decoded' => 'a▮b',
            ],
            730 => [
                'encoded' => 'a&mcomma;b',
                'decoded' => 'a⨩b',
            ],
            731 => [
                'encoded' => 'a&Mcy;b',
                'decoded' => 'aМb',
            ],
            732 => [
                'encoded' => 'a&mcy;b',
                'decoded' => 'aмb',
            ],
            733 => [
                'encoded' => 'a&mdash;b',
                'decoded' => 'a—b',
            ],
            734 => [
                'encoded' => 'a&mDDot;b',
                'decoded' => 'a∺b',
            ],
            735 => [
                'encoded' => 'a&MediumSpace;b',
                'decoded' => 'a b',
            ],
            736 => [
                'encoded' => 'a&Mfr;b',
                'decoded' => 'a𝔐b',
            ],
            737 => [
                'encoded' => 'a&mfr;b',
                'decoded' => 'a𝔪b',
            ],
            738 => [
                'encoded' => 'a&mho;b',
                'decoded' => 'a℧b',
            ],
            739 => [
                'encoded' => 'a&micro;b',
                'decoded' => 'aµb',
            ],
            740 => [
                'encoded' => 'a&midcir;b',
                'decoded' => 'a⫰b',
            ],
            741 => [
                'encoded' => 'a&mid;b',
                'decoded' => 'a∣b',
            ],
            742 => [
                'encoded' => 'a&middot;b',
                'decoded' => 'a·b',
            ],
            743 => [
                'encoded' => 'a&minusb;b',
                'decoded' => 'a⊟b',
            ],
            744 => [
                'encoded' => 'a&minus;b',
                'decoded' => 'a−b',
            ],
            745 => [
                'encoded' => 'a&minusd;b',
                'decoded' => 'a∸b',
            ],
            746 => [
                'encoded' => 'a&minusdu;b',
                'decoded' => 'a⨪b',
            ],
            747 => [
                'encoded' => 'a&mlcp;b',
                'decoded' => 'a⫛b',
            ],
            748 => [
                'encoded' => 'a&mldr;b',
                'decoded' => 'a…b',
            ],
            749 => [
                'encoded' => 'a&models;b',
                'decoded' => 'a⊧b',
            ],
            750 => [
                'encoded' => 'a&Mopf;b',
                'decoded' => 'a𝕄b',
            ],
            751 => [
                'encoded' => 'a&mopf;b',
                'decoded' => 'a𝕞b',
            ],
            752 => [
                'encoded' => 'a&mp;b',
                'decoded' => 'a∓b',
            ],
            753 => [
                'encoded' => 'a&mscr;b',
                'decoded' => 'a𝓂b',
            ],
            754 => [
                'encoded' => 'a&Mscr;b',
                'decoded' => 'aℳb',
            ],
            755 => [
                'encoded' => 'a&Mu;b',
                'decoded' => 'aΜb',
            ],
            756 => [
                'encoded' => 'a&mu;b',
                'decoded' => 'aμb',
            ],
            757 => [
                'encoded' => 'a&mumap;b',
                'decoded' => 'a⊸b',
            ],
            758 => [
                'encoded' => 'a&Nacute;b',
                'decoded' => 'aŃb',
            ],
            759 => [
                'encoded' => 'a&nacute;b',
                'decoded' => 'ańb',
            ],
            760 => [
                'encoded' => 'a&nang;b',
                'decoded' => 'a∠⃒b',
            ],
            761 => [
                'encoded' => 'a&nap;b',
                'decoded' => 'a≉b',
            ],
            762 => [
                'encoded' => 'a&napE;b',
                'decoded' => 'a⩰̸b',
            ],
            763 => [
                'encoded' => 'a&napid;b',
                'decoded' => 'a≋̸b',
            ],
            764 => [
                'encoded' => 'a&napos;b',
                'decoded' => 'aŉb',
            ],
            765 => [
                'encoded' => 'a&natur;b',
                'decoded' => 'a♮b',
            ],
            766 => [
                'encoded' => 'a&nbsp;b',
                'decoded' => 'a b',
            ],
            767 => [
                'encoded' => 'a&nbump;b',
                'decoded' => 'a≎̸b',
            ],
            768 => [
                'encoded' => 'a&nbumpe;b',
                'decoded' => 'a≏̸b',
            ],
            769 => [
                'encoded' => 'a&ncap;b',
                'decoded' => 'a⩃b',
            ],
            770 => [
                'encoded' => 'a&Ncaron;b',
                'decoded' => 'aŇb',
            ],
            771 => [
                'encoded' => 'a&ncaron;b',
                'decoded' => 'aňb',
            ],
            772 => [
                'encoded' => 'a&Ncedil;b',
                'decoded' => 'aŅb',
            ],
            773 => [
                'encoded' => 'a&ncedil;b',
                'decoded' => 'aņb',
            ],
            774 => [
                'encoded' => 'a&ncong;b',
                'decoded' => 'a≇b',
            ],
            775 => [
                'encoded' => 'a&ncongdot;b',
                'decoded' => 'a⩭̸b',
            ],
            776 => [
                'encoded' => 'a&ncup;b',
                'decoded' => 'a⩂b',
            ],
            777 => [
                'encoded' => 'a&Ncy;b',
                'decoded' => 'aНb',
            ],
            778 => [
                'encoded' => 'a&ncy;b',
                'decoded' => 'aнb',
            ],
            779 => [
                'encoded' => 'a&ndash;b',
                'decoded' => 'a–b',
            ],
            780 => [
                'encoded' => 'a&nearhk;b',
                'decoded' => 'a⤤b',
            ],
            781 => [
                'encoded' => 'a&nearr;b',
                'decoded' => 'a↗b',
            ],
            782 => [
                'encoded' => 'a&neArr;b',
                'decoded' => 'a⇗b',
            ],
            783 => [
                'encoded' => 'a&ne;b',
                'decoded' => 'a≠b',
            ],
            784 => [
                'encoded' => 'a&nedot;b',
                'decoded' => 'a≐̸b',
            ],
            785 => [
                'encoded' => 'a&nequiv;b',
                'decoded' => 'a≢b',
            ],
            786 => [
                'encoded' => 'a&nesim;b',
                'decoded' => 'a≂̸b',
            ],
            787 => [
                'encoded' => 'a
b',
                'decoded' => 'a
b',
            ],
            788 => [
                'encoded' => 'a&nexist;b',
                'decoded' => 'a∄b',
            ],
            789 => [
                'encoded' => 'a&Nfr;b',
                'decoded' => 'a𝔑b',
            ],
            790 => [
                'encoded' => 'a&nfr;b',
                'decoded' => 'a𝔫b',
            ],
            791 => [
                'encoded' => 'a&ngE;b',
                'decoded' => 'a≧̸b',
            ],
            792 => [
                'encoded' => 'a&nge;b',
                'decoded' => 'a≱b',
            ],
            793 => [
                'encoded' => 'a&nges;b',
                'decoded' => 'a⩾̸b',
            ],
            794 => [
                'encoded' => 'a&nGg;b',
                'decoded' => 'a⋙̸b',
            ],
            795 => [
                'encoded' => 'a&ngsim;b',
                'decoded' => 'a≵b',
            ],
            796 => [
                'encoded' => 'a&nGt;b',
                'decoded' => 'a≫⃒b',
            ],
            797 => [
                'encoded' => 'a&ngt;b',
                'decoded' => 'a≯b',
            ],
            798 => [
                'encoded' => 'a&nGtv;b',
                'decoded' => 'a≫̸b',
            ],
            799 => [
                'encoded' => 'a&nharr;b',
                'decoded' => 'a↮b',
            ],
            800 => [
                'encoded' => 'a&nhArr;b',
                'decoded' => 'a⇎b',
            ],
            801 => [
                'encoded' => 'a&nhpar;b',
                'decoded' => 'a⫲b',
            ],
            802 => [
                'encoded' => 'a&ni;b',
                'decoded' => 'a∋b',
            ],
            803 => [
                'encoded' => 'a&nis;b',
                'decoded' => 'a⋼b',
            ],
            804 => [
                'encoded' => 'a&nisd;b',
                'decoded' => 'a⋺b',
            ],
            805 => [
                'encoded' => 'a&NJcy;b',
                'decoded' => 'aЊb',
            ],
            806 => [
                'encoded' => 'a&njcy;b',
                'decoded' => 'aњb',
            ],
            807 => [
                'encoded' => 'a&nlarr;b',
                'decoded' => 'a↚b',
            ],
            808 => [
                'encoded' => 'a&nlArr;b',
                'decoded' => 'a⇍b',
            ],
            809 => [
                'encoded' => 'a&nldr;b',
                'decoded' => 'a‥b',
            ],
            810 => [
                'encoded' => 'a&nlE;b',
                'decoded' => 'a≦̸b',
            ],
            811 => [
                'encoded' => 'a&nle;b',
                'decoded' => 'a≰b',
            ],
            812 => [
                'encoded' => 'a&nles;b',
                'decoded' => 'a⩽̸b',
            ],
            813 => [
                'encoded' => 'a&nLl;b',
                'decoded' => 'a⋘̸b',
            ],
            814 => [
                'encoded' => 'a&nlsim;b',
                'decoded' => 'a≴b',
            ],
            815 => [
                'encoded' => 'a&nLt;b',
                'decoded' => 'a≪⃒b',
            ],
            816 => [
                'encoded' => 'a&nlt;b',
                'decoded' => 'a≮b',
            ],
            817 => [
                'encoded' => 'a&nltri;b',
                'decoded' => 'a⋪b',
            ],
            818 => [
                'encoded' => 'a&nltrie;b',
                'decoded' => 'a⋬b',
            ],
            819 => [
                'encoded' => 'a&nLtv;b',
                'decoded' => 'a≪̸b',
            ],
            820 => [
                'encoded' => 'a&nmid;b',
                'decoded' => 'a∤b',
            ],
            821 => [
                'encoded' => 'a&NoBreak;b',
                'decoded' => 'ab',
            ],
            822 => [
                'encoded' => 'a&nopf;b',
                'decoded' => 'a𝕟b',
            ],
            823 => [
                'encoded' => 'a&Nopf;b',
                'decoded' => 'aℕb',
            ],
            824 => [
                'encoded' => 'a&Not;b',
                'decoded' => 'a⫬b',
            ],
            825 => [
                'encoded' => 'a&not;b',
                'decoded' => 'a¬b',
            ],
            826 => [
                'encoded' => 'a&NotCupCap;b',
                'decoded' => 'a≭b',
            ],
            827 => [
                'encoded' => 'a&notin;b',
                'decoded' => 'a∉b',
            ],
            828 => [
                'encoded' => 'a&notindot;b',
                'decoded' => 'a⋵̸b',
            ],
            829 => [
                'encoded' => 'a&notinE;b',
                'decoded' => 'a⋹̸b',
            ],
            830 => [
                'encoded' => 'a&notinvb;b',
                'decoded' => 'a⋷b',
            ],
            831 => [
                'encoded' => 'a&notinvc;b',
                'decoded' => 'a⋶b',
            ],
            832 => [
                'encoded' => 'a&NotLeftTriangleBar;b',
                'decoded' => 'a⧏̸b',
            ],
            833 => [
                'encoded' => 'a&NotNestedGreaterGreater;b',
                'decoded' => 'a⪢̸b',
            ],
            834 => [
                'encoded' => 'a&NotNestedLessLess;b',
                'decoded' => 'a⪡̸b',
            ],
            835 => [
                'encoded' => 'a&notni;b',
                'decoded' => 'a∌b',
            ],
            836 => [
                'encoded' => 'a&notnivb;b',
                'decoded' => 'a⋾b',
            ],
            837 => [
                'encoded' => 'a&notnivc;b',
                'decoded' => 'a⋽b',
            ],
            838 => [
                'encoded' => 'a&NotRightTriangleBar;b',
                'decoded' => 'a⧐̸b',
            ],
            839 => [
                'encoded' => 'a&NotSquareSubset;b',
                'decoded' => 'a⊏̸b',
            ],
            840 => [
                'encoded' => 'a&NotSquareSuperset;b',
                'decoded' => 'a⊐̸b',
            ],
            841 => [
                'encoded' => 'a&NotSucceedsTilde;b',
                'decoded' => 'a≿̸b',
            ],
            842 => [
                'encoded' => 'a&npar;b',
                'decoded' => 'a∦b',
            ],
            843 => [
                'encoded' => 'a&nparsl;b',
                'decoded' => 'a⫽⃥b',
            ],
            844 => [
                'encoded' => 'a&npart;b',
                'decoded' => 'a∂̸b',
            ],
            845 => [
                'encoded' => 'a&npolint;b',
                'decoded' => 'a⨔b',
            ],
            846 => [
                'encoded' => 'a&npr;b',
                'decoded' => 'a⊀b',
            ],
            847 => [
                'encoded' => 'a&nprcue;b',
                'decoded' => 'a⋠b',
            ],
            848 => [
                'encoded' => 'a&npre;b',
                'decoded' => 'a⪯̸b',
            ],
            849 => [
                'encoded' => 'a&nrarrc;b',
                'decoded' => 'a⤳̸b',
            ],
            850 => [
                'encoded' => 'a&nrarr;b',
                'decoded' => 'a↛b',
            ],
            851 => [
                'encoded' => 'a&nrArr;b',
                'decoded' => 'a⇏b',
            ],
            852 => [
                'encoded' => 'a&nrarrw;b',
                'decoded' => 'a↝̸b',
            ],
            853 => [
                'encoded' => 'a&nrtri;b',
                'decoded' => 'a⋫b',
            ],
            854 => [
                'encoded' => 'a&nrtrie;b',
                'decoded' => 'a⋭b',
            ],
            855 => [
                'encoded' => 'a&nsc;b',
                'decoded' => 'a⊁b',
            ],
            856 => [
                'encoded' => 'a&nsccue;b',
                'decoded' => 'a⋡b',
            ],
            857 => [
                'encoded' => 'a&nsce;b',
                'decoded' => 'a⪰̸b',
            ],
            858 => [
                'encoded' => 'a&Nscr;b',
                'decoded' => 'a𝒩b',
            ],
            859 => [
                'encoded' => 'a&nscr;b',
                'decoded' => 'a𝓃b',
            ],
            860 => [
                'encoded' => 'a&nsim;b',
                'decoded' => 'a≁b',
            ],
            861 => [
                'encoded' => 'a&nsime;b',
                'decoded' => 'a≄b',
            ],
            862 => [
                'encoded' => 'a&nsqsube;b',
                'decoded' => 'a⋢b',
            ],
            863 => [
                'encoded' => 'a&nsqsupe;b',
                'decoded' => 'a⋣b',
            ],
            864 => [
                'encoded' => 'a&nsub;b',
                'decoded' => 'a⊄b',
            ],
            865 => [
                'encoded' => 'a&nsubE;b',
                'decoded' => 'a⫅̸b',
            ],
            866 => [
                'encoded' => 'a&nsube;b',
                'decoded' => 'a⊈b',
            ],
            867 => [
                'encoded' => 'a&nsup;b',
                'decoded' => 'a⊅b',
            ],
            868 => [
                'encoded' => 'a&nsupE;b',
                'decoded' => 'a⫆̸b',
            ],
            869 => [
                'encoded' => 'a&nsupe;b',
                'decoded' => 'a⊉b',
            ],
            870 => [
                'encoded' => 'a&ntgl;b',
                'decoded' => 'a≹b',
            ],
            871 => [
                'encoded' => 'a&Ntilde;b',
                'decoded' => 'aÑb',
            ],
            872 => [
                'encoded' => 'a&ntilde;b',
                'decoded' => 'añb',
            ],
            873 => [
                'encoded' => 'a&ntlg;b',
                'decoded' => 'a≸b',
            ],
            874 => [
                'encoded' => 'a&Nu;b',
                'decoded' => 'aΝb',
            ],
            875 => [
                'encoded' => 'a&nu;b',
                'decoded' => 'aνb',
            ],
            876 => [
                'encoded' => 'a&numero;b',
                'decoded' => 'a№b',
            ],
            877 => [
                'encoded' => 'a&numsp;b',
                'decoded' => 'a b',
            ],
            878 => [
                'encoded' => 'a&nvap;b',
                'decoded' => 'a≍⃒b',
            ],
            879 => [
                'encoded' => 'a&nvdash;b',
                'decoded' => 'a⊬b',
            ],
            880 => [
                'encoded' => 'a&nvDash;b',
                'decoded' => 'a⊭b',
            ],
            881 => [
                'encoded' => 'a&nVdash;b',
                'decoded' => 'a⊮b',
            ],
            882 => [
                'encoded' => 'a&nVDash;b',
                'decoded' => 'a⊯b',
            ],
            883 => [
                'encoded' => 'a&nvge;b',
                'decoded' => 'a≥⃒b',
            ],
            884 => [
                'encoded' => 'a&nvgt;b',
                'decoded' => 'a>⃒b',
            ],
            885 => [
                'encoded' => 'a&nvHarr;b',
                'decoded' => 'a⤄b',
            ],
            886 => [
                'encoded' => 'a&nvinfin;b',
                'decoded' => 'a⧞b',
            ],
            887 => [
                'encoded' => 'a&nvlArr;b',
                'decoded' => 'a⤂b',
            ],
            888 => [
                'encoded' => 'a&nvle;b',
                'decoded' => 'a≤⃒b',
            ],
            889 => [
                'encoded' => 'a&nvlt;b',
                'decoded' => 'a<⃒b',
            ],
            890 => [
                'encoded' => 'a&nvltrie;b',
                'decoded' => 'a⊴⃒b',
            ],
            891 => [
                'encoded' => 'a&nvrArr;b',
                'decoded' => 'a⤃b',
            ],
            892 => [
                'encoded' => 'a&nvrtrie;b',
                'decoded' => 'a⊵⃒b',
            ],
            893 => [
                'encoded' => 'a&nvsim;b',
                'decoded' => 'a∼⃒b',
            ],
            894 => [
                'encoded' => 'a&nwarhk;b',
                'decoded' => 'a⤣b',
            ],
            895 => [
                'encoded' => 'a&nwarr;b',
                'decoded' => 'a↖b',
            ],
            896 => [
                'encoded' => 'a&nwArr;b',
                'decoded' => 'a⇖b',
            ],
            897 => [
                'encoded' => 'a&nwnear;b',
                'decoded' => 'a⤧b',
            ],
            898 => [
                'encoded' => 'a&Oacute;b',
                'decoded' => 'aÓb',
            ],
            899 => [
                'encoded' => 'a&oacute;b',
                'decoded' => 'aób',
            ],
            900 => [
                'encoded' => 'a&oast;b',
                'decoded' => 'a⊛b',
            ],
            901 => [
                'encoded' => 'a&Ocirc;b',
                'decoded' => 'aÔb',
            ],
            902 => [
                'encoded' => 'a&ocirc;b',
                'decoded' => 'aôb',
            ],
            903 => [
                'encoded' => 'a&ocir;b',
                'decoded' => 'a⊚b',
            ],
            904 => [
                'encoded' => 'a&Ocy;b',
                'decoded' => 'aОb',
            ],
            905 => [
                'encoded' => 'a&ocy;b',
                'decoded' => 'aоb',
            ],
            906 => [
                'encoded' => 'a&odash;b',
                'decoded' => 'a⊝b',
            ],
            907 => [
                'encoded' => 'a&Odblac;b',
                'decoded' => 'aŐb',
            ],
            908 => [
                'encoded' => 'a&odblac;b',
                'decoded' => 'aőb',
            ],
            909 => [
                'encoded' => 'a&odiv;b',
                'decoded' => 'a⨸b',
            ],
            910 => [
                'encoded' => 'a&odot;b',
                'decoded' => 'a⊙b',
            ],
            911 => [
                'encoded' => 'a&odsold;b',
                'decoded' => 'a⦼b',
            ],
            912 => [
                'encoded' => 'a&OElig;b',
                'decoded' => 'aŒb',
            ],
            913 => [
                'encoded' => 'a&oelig;b',
                'decoded' => 'aœb',
            ],
            914 => [
                'encoded' => 'a&ofcir;b',
                'decoded' => 'a⦿b',
            ],
            915 => [
                'encoded' => 'a&Ofr;b',
                'decoded' => 'a𝔒b',
            ],
            916 => [
                'encoded' => 'a&ofr;b',
                'decoded' => 'a𝔬b',
            ],
            917 => [
                'encoded' => 'a&ogon;b',
                'decoded' => 'a˛b',
            ],
            918 => [
                'encoded' => 'a&Ograve;b',
                'decoded' => 'aÒb',
            ],
            919 => [
                'encoded' => 'a&ograve;b',
                'decoded' => 'aòb',
            ],
            920 => [
                'encoded' => 'a&ogt;b',
                'decoded' => 'a⧁b',
            ],
            921 => [
                'encoded' => 'a&ohbar;b',
                'decoded' => 'a⦵b',
            ],
            922 => [
                'encoded' => 'a&ohm;b',
                'decoded' => 'aΩb',
            ],
            923 => [
                'encoded' => 'a&oint;b',
                'decoded' => 'a∮b',
            ],
            924 => [
                'encoded' => 'a&olarr;b',
                'decoded' => 'a↺b',
            ],
            925 => [
                'encoded' => 'a&olcir;b',
                'decoded' => 'a⦾b',
            ],
            926 => [
                'encoded' => 'a&olcross;b',
                'decoded' => 'a⦻b',
            ],
            927 => [
                'encoded' => 'a&oline;b',
                'decoded' => 'a‾b',
            ],
            928 => [
                'encoded' => 'a&olt;b',
                'decoded' => 'a⧀b',
            ],
            929 => [
                'encoded' => 'a&Omacr;b',
                'decoded' => 'aŌb',
            ],
            930 => [
                'encoded' => 'a&omacr;b',
                'decoded' => 'aōb',
            ],
            931 => [
                'encoded' => 'a&omega;b',
                'decoded' => 'aωb',
            ],
            932 => [
                'encoded' => 'a&Omicron;b',
                'decoded' => 'aΟb',
            ],
            933 => [
                'encoded' => 'a&omicron;b',
                'decoded' => 'aοb',
            ],
            934 => [
                'encoded' => 'a&omid;b',
                'decoded' => 'a⦶b',
            ],
            935 => [
                'encoded' => 'a&ominus;b',
                'decoded' => 'a⊖b',
            ],
            936 => [
                'encoded' => 'a&Oopf;b',
                'decoded' => 'a𝕆b',
            ],
            937 => [
                'encoded' => 'a&oopf;b',
                'decoded' => 'a𝕠b',
            ],
            938 => [
                'encoded' => 'a&opar;b',
                'decoded' => 'a⦷b',
            ],
            939 => [
                'encoded' => 'a&operp;b',
                'decoded' => 'a⦹b',
            ],
            940 => [
                'encoded' => 'a&oplus;b',
                'decoded' => 'a⊕b',
            ],
            941 => [
                'encoded' => 'a&orarr;b',
                'decoded' => 'a↻b',
            ],
            942 => [
                'encoded' => 'a&Or;b',
                'decoded' => 'a⩔b',
            ],
            943 => [
                'encoded' => 'a&or;b',
                'decoded' => 'a∨b',
            ],
            944 => [
                'encoded' => 'a&ord;b',
                'decoded' => 'a⩝b',
            ],
            945 => [
                'encoded' => 'a&ordf;b',
                'decoded' => 'aªb',
            ],
            946 => [
                'encoded' => 'a&ordm;b',
                'decoded' => 'aºb',
            ],
            947 => [
                'encoded' => 'a&origof;b',
                'decoded' => 'a⊶b',
            ],
            948 => [
                'encoded' => 'a&oror;b',
                'decoded' => 'a⩖b',
            ],
            949 => [
                'encoded' => 'a&orslope;b',
                'decoded' => 'a⩗b',
            ],
            950 => [
                'encoded' => 'a&orv;b',
                'decoded' => 'a⩛b',
            ],
            951 => [
                'encoded' => 'a&oS;b',
                'decoded' => 'aⓈb',
            ],
            952 => [
                'encoded' => 'a&Oscr;b',
                'decoded' => 'a𝒪b',
            ],
            953 => [
                'encoded' => 'a&oscr;b',
                'decoded' => 'aℴb',
            ],
            954 => [
                'encoded' => 'a&Oslash;b',
                'decoded' => 'aØb',
            ],
            955 => [
                'encoded' => 'a&oslash;b',
                'decoded' => 'aøb',
            ],
            956 => [
                'encoded' => 'a&osol;b',
                'decoded' => 'a⊘b',
            ],
            957 => [
                'encoded' => 'a&Otilde;b',
                'decoded' => 'aÕb',
            ],
            958 => [
                'encoded' => 'a&otilde;b',
                'decoded' => 'aõb',
            ],
            959 => [
                'encoded' => 'a&otimesas;b',
                'decoded' => 'a⨶b',
            ],
            960 => [
                'encoded' => 'a&Otimes;b',
                'decoded' => 'a⨷b',
            ],
            961 => [
                'encoded' => 'a&otimes;b',
                'decoded' => 'a⊗b',
            ],
            962 => [
                'encoded' => 'a&Ouml;b',
                'decoded' => 'aÖb',
            ],
            963 => [
                'encoded' => 'a&ouml;b',
                'decoded' => 'aöb',
            ],
            964 => [
                'encoded' => 'a&ovbar;b',
                'decoded' => 'a⌽b',
            ],
            965 => [
                'encoded' => 'a&OverBrace;b',
                'decoded' => 'a⏞b',
            ],
            966 => [
                'encoded' => 'a&OverParenthesis;b',
                'decoded' => 'a⏜b',
            ],
            967 => [
                'encoded' => 'a&para;b',
                'decoded' => 'a¶b',
            ],
            968 => [
                'encoded' => 'a&par;b',
                'decoded' => 'a∥b',
            ],
            969 => [
                'encoded' => 'a&parsim;b',
                'decoded' => 'a⫳b',
            ],
            970 => [
                'encoded' => 'a&parsl;b',
                'decoded' => 'a⫽b',
            ],
            971 => [
                'encoded' => 'a&part;b',
                'decoded' => 'a∂b',
            ],
            972 => [
                'encoded' => 'a&Pcy;b',
                'decoded' => 'aПb',
            ],
            973 => [
                'encoded' => 'a&pcy;b',
                'decoded' => 'aпb',
            ],
            974 => [
                'encoded' => 'a&permil;b',
                'decoded' => 'a‰b',
            ],
            975 => [
                'encoded' => 'a&pertenk;b',
                'decoded' => 'a‱b',
            ],
            976 => [
                'encoded' => 'a&Pfr;b',
                'decoded' => 'a𝔓b',
            ],
            977 => [
                'encoded' => 'a&pfr;b',
                'decoded' => 'a𝔭b',
            ],
            978 => [
                'encoded' => 'a&Phi;b',
                'decoded' => 'aΦb',
            ],
            979 => [
                'encoded' => 'a&phi;b',
                'decoded' => 'aφb',
            ],
            980 => [
                'encoded' => 'a&phiv;b',
                'decoded' => 'aϕb',
            ],
            981 => [
                'encoded' => 'a&phone;b',
                'decoded' => 'a☎b',
            ],
            982 => [
                'encoded' => 'a&Pi;b',
                'decoded' => 'aΠb',
            ],
            983 => [
                'encoded' => 'a&pi;b',
                'decoded' => 'aπb',
            ],
            984 => [
                'encoded' => 'a&piv;b',
                'decoded' => 'aϖb',
            ],
            985 => [
                'encoded' => 'a&planckh;b',
                'decoded' => 'aℎb',
            ],
            986 => [
                'encoded' => 'a&plusacir;b',
                'decoded' => 'a⨣b',
            ],
            987 => [
                'encoded' => 'a&plusb;b',
                'decoded' => 'a⊞b',
            ],
            988 => [
                'encoded' => 'a&pluscir;b',
                'decoded' => 'a⨢b',
            ],
            989 => [
                'encoded' => 'a&plusdo;b',
                'decoded' => 'a∔b',
            ],
            990 => [
                'encoded' => 'a&plusdu;b',
                'decoded' => 'a⨥b',
            ],
            991 => [
                'encoded' => 'a&pluse;b',
                'decoded' => 'a⩲b',
            ],
            992 => [
                'encoded' => 'a&plussim;b',
                'decoded' => 'a⨦b',
            ],
            993 => [
                'encoded' => 'a&plustwo;b',
                'decoded' => 'a⨧b',
            ],
            994 => [
                'encoded' => 'a&pm;b',
                'decoded' => 'a±b',
            ],
            995 => [
                'encoded' => 'a&pointint;b',
                'decoded' => 'a⨕b',
            ],
            996 => [
                'encoded' => 'a&popf;b',
                'decoded' => 'a𝕡b',
            ],
            997 => [
                'encoded' => 'a&Popf;b',
                'decoded' => 'aℙb',
            ],
            998 => [
                'encoded' => 'a&pound;b',
                'decoded' => 'a£b',
            ],
            999 => [
                'encoded' => 'a&prap;b',
                'decoded' => 'a⪷b',
            ],
            1000 => [
                'encoded' => 'a&Pr;b',
                'decoded' => 'a⪻b',
            ],
            1001 => [
                'encoded' => 'a&pr;b',
                'decoded' => 'a≺b',
            ],
            1002 => [
                'encoded' => 'a&prcue;b',
                'decoded' => 'a≼b',
            ],
            1003 => [
                'encoded' => 'a&pre;b',
                'decoded' => 'a⪯b',
            ],
            1004 => [
                'encoded' => 'a&prE;b',
                'decoded' => 'a⪳b',
            ],
            1005 => [
                'encoded' => 'a&prime;b',
                'decoded' => 'a′b',
            ],
            1006 => [
                'encoded' => 'a&Prime;b',
                'decoded' => 'a″b',
            ],
            1007 => [
                'encoded' => 'a&prnap;b',
                'decoded' => 'a⪹b',
            ],
            1008 => [
                'encoded' => 'a&prnE;b',
                'decoded' => 'a⪵b',
            ],
            1009 => [
                'encoded' => 'a&prnsim;b',
                'decoded' => 'a⋨b',
            ],
            1010 => [
                'encoded' => 'a&prod;b',
                'decoded' => 'a∏b',
            ],
            1011 => [
                'encoded' => 'a&profalar;b',
                'decoded' => 'a⌮b',
            ],
            1012 => [
                'encoded' => 'a&profline;b',
                'decoded' => 'a⌒b',
            ],
            1013 => [
                'encoded' => 'a&profsurf;b',
                'decoded' => 'a⌓b',
            ],
            1014 => [
                'encoded' => 'a&prop;b',
                'decoded' => 'a∝b',
            ],
            1015 => [
                'encoded' => 'a&prsim;b',
                'decoded' => 'a≾b',
            ],
            1016 => [
                'encoded' => 'a&prurel;b',
                'decoded' => 'a⊰b',
            ],
            1017 => [
                'encoded' => 'a&Pscr;b',
                'decoded' => 'a𝒫b',
            ],
            1018 => [
                'encoded' => 'a&pscr;b',
                'decoded' => 'a𝓅b',
            ],
            1019 => [
                'encoded' => 'a&Psi;b',
                'decoded' => 'aΨb',
            ],
            1020 => [
                'encoded' => 'a&psi;b',
                'decoded' => 'aψb',
            ],
            1021 => [
                'encoded' => 'a&puncsp;b',
                'decoded' => 'a b',
            ],
            1022 => [
                'encoded' => 'a&Qfr;b',
                'decoded' => 'a𝔔b',
            ],
            1023 => [
                'encoded' => 'a&qfr;b',
                'decoded' => 'a𝔮b',
            ],
            1024 => [
                'encoded' => 'a&qint;b',
                'decoded' => 'a⨌b',
            ],
            1025 => [
                'encoded' => 'a&qopf;b',
                'decoded' => 'a𝕢b',
            ],
            1026 => [
                'encoded' => 'a&Qopf;b',
                'decoded' => 'aℚb',
            ],
            1027 => [
                'encoded' => 'a&qprime;b',
                'decoded' => 'a⁗b',
            ],
            1028 => [
                'encoded' => 'a&Qscr;b',
                'decoded' => 'a𝒬b',
            ],
            1029 => [
                'encoded' => 'a&qscr;b',
                'decoded' => 'a𝓆b',
            ],
            1030 => [
                'encoded' => 'a&quatint;b',
                'decoded' => 'a⨖b',
            ],
            1031 => [
                'encoded' => 'a&quot;b',
                'decoded' => 'a"b',
            ],
            1032 => [
                'encoded' => 'a&rAarr;b',
                'decoded' => 'a⇛b',
            ],
            1033 => [
                'encoded' => 'a&race;b',
                'decoded' => 'a∽̱b',
            ],
            1034 => [
                'encoded' => 'a&Racute;b',
                'decoded' => 'aŔb',
            ],
            1035 => [
                'encoded' => 'a&racute;b',
                'decoded' => 'aŕb',
            ],
            1036 => [
                'encoded' => 'a&raemptyv;b',
                'decoded' => 'a⦳b',
            ],
            1037 => [
                'encoded' => 'a&rang;b',
                'decoded' => 'a⟩b',
            ],
            1038 => [
                'encoded' => 'a&Rang;b',
                'decoded' => 'a⟫b',
            ],
            1039 => [
                'encoded' => 'a&rangd;b',
                'decoded' => 'a⦒b',
            ],
            1040 => [
                'encoded' => 'a&range;b',
                'decoded' => 'a⦥b',
            ],
            1041 => [
                'encoded' => 'a&raquo;b',
                'decoded' => 'a»b',
            ],
            1042 => [
                'encoded' => 'a&rarrap;b',
                'decoded' => 'a⥵b',
            ],
            1043 => [
                'encoded' => 'a&rarrb;b',
                'decoded' => 'a⇥b',
            ],
            1044 => [
                'encoded' => 'a&rarrbfs;b',
                'decoded' => 'a⤠b',
            ],
            1045 => [
                'encoded' => 'a&rarrc;b',
                'decoded' => 'a⤳b',
            ],
            1046 => [
                'encoded' => 'a&rarr;b',
                'decoded' => 'a→b',
            ],
            1047 => [
                'encoded' => 'a&Rarr;b',
                'decoded' => 'a↠b',
            ],
            1048 => [
                'encoded' => 'a&rArr;b',
                'decoded' => 'a⇒b',
            ],
            1049 => [
                'encoded' => 'a&rarrfs;b',
                'decoded' => 'a⤞b',
            ],
            1050 => [
                'encoded' => 'a&rarrhk;b',
                'decoded' => 'a↪b',
            ],
            1051 => [
                'encoded' => 'a&rarrlp;b',
                'decoded' => 'a↬b',
            ],
            1052 => [
                'encoded' => 'a&rarrpl;b',
                'decoded' => 'a⥅b',
            ],
            1053 => [
                'encoded' => 'a&rarrsim;b',
                'decoded' => 'a⥴b',
            ],
            1054 => [
                'encoded' => 'a&Rarrtl;b',
                'decoded' => 'a⤖b',
            ],
            1055 => [
                'encoded' => 'a&rarrtl;b',
                'decoded' => 'a↣b',
            ],
            1056 => [
                'encoded' => 'a&rarrw;b',
                'decoded' => 'a↝b',
            ],
            1057 => [
                'encoded' => 'a&ratail;b',
                'decoded' => 'a⤚b',
            ],
            1058 => [
                'encoded' => 'a&rAtail;b',
                'decoded' => 'a⤜b',
            ],
            1059 => [
                'encoded' => 'a&ratio;b',
                'decoded' => 'a∶b',
            ],
            1060 => [
                'encoded' => 'a&rbarr;b',
                'decoded' => 'a⤍b',
            ],
            1061 => [
                'encoded' => 'a&rBarr;b',
                'decoded' => 'a⤏b',
            ],
            1062 => [
                'encoded' => 'a&RBarr;b',
                'decoded' => 'a⤐b',
            ],
            1063 => [
                'encoded' => 'a&rbbrk;b',
                'decoded' => 'a❳b',
            ],
            1064 => [
                'encoded' => 'a&rbrke;b',
                'decoded' => 'a⦌b',
            ],
            1065 => [
                'encoded' => 'a&rbrksld;b',
                'decoded' => 'a⦎b',
            ],
            1066 => [
                'encoded' => 'a&rbrkslu;b',
                'decoded' => 'a⦐b',
            ],
            1067 => [
                'encoded' => 'a&Rcaron;b',
                'decoded' => 'aŘb',
            ],
            1068 => [
                'encoded' => 'a&rcaron;b',
                'decoded' => 'ařb',
            ],
            1069 => [
                'encoded' => 'a&Rcedil;b',
                'decoded' => 'aŖb',
            ],
            1070 => [
                'encoded' => 'a&rcedil;b',
                'decoded' => 'aŗb',
            ],
            1071 => [
                'encoded' => 'a&rceil;b',
                'decoded' => 'a⌉b',
            ],
            1072 => [
                'encoded' => 'a&Rcy;b',
                'decoded' => 'aРb',
            ],
            1073 => [
                'encoded' => 'a&rcy;b',
                'decoded' => 'aрb',
            ],
            1074 => [
                'encoded' => 'a&rdca;b',
                'decoded' => 'a⤷b',
            ],
            1075 => [
                'encoded' => 'a&rdldhar;b',
                'decoded' => 'a⥩b',
            ],
            1076 => [
                'encoded' => 'a&rdquo;b',
                'decoded' => 'a”b',
            ],
            1077 => [
                'encoded' => 'a&rdsh;b',
                'decoded' => 'a↳b',
            ],
            1078 => [
                'encoded' => 'a&Re;b',
                'decoded' => 'aℜb',
            ],
            1079 => [
                'encoded' => 'a&rect;b',
                'decoded' => 'a▭b',
            ],
            1080 => [
                'encoded' => 'a&reg;b',
                'decoded' => 'a®b',
            ],
            1081 => [
                'encoded' => 'a&rfisht;b',
                'decoded' => 'a⥽b',
            ],
            1082 => [
                'encoded' => 'a&rfloor;b',
                'decoded' => 'a⌋b',
            ],
            1083 => [
                'encoded' => 'a&rfr;b',
                'decoded' => 'a𝔯b',
            ],
            1084 => [
                'encoded' => 'a&rHar;b',
                'decoded' => 'a⥤b',
            ],
            1085 => [
                'encoded' => 'a&rhard;b',
                'decoded' => 'a⇁b',
            ],
            1086 => [
                'encoded' => 'a&rharu;b',
                'decoded' => 'a⇀b',
            ],
            1087 => [
                'encoded' => 'a&rharul;b',
                'decoded' => 'a⥬b',
            ],
            1088 => [
                'encoded' => 'a&Rho;b',
                'decoded' => 'aΡb',
            ],
            1089 => [
                'encoded' => 'a&rho;b',
                'decoded' => 'aρb',
            ],
            1090 => [
                'encoded' => 'a&rhov;b',
                'decoded' => 'aϱb',
            ],
            1091 => [
                'encoded' => 'a&RightDownTeeVector;b',
                'decoded' => 'a⥝b',
            ],
            1092 => [
                'encoded' => 'a&RightDownVectorBar;b',
                'decoded' => 'a⥕b',
            ],
            1093 => [
                'encoded' => 'a&RightTeeVector;b',
                'decoded' => 'a⥛b',
            ],
            1094 => [
                'encoded' => 'a&RightTriangleBar;b',
                'decoded' => 'a⧐b',
            ],
            1095 => [
                'encoded' => 'a&RightUpDownVector;b',
                'decoded' => 'a⥏b',
            ],
            1096 => [
                'encoded' => 'a&RightUpTeeVector;b',
                'decoded' => 'a⥜b',
            ],
            1097 => [
                'encoded' => 'a&RightUpVectorBar;b',
                'decoded' => 'a⥔b',
            ],
            1098 => [
                'encoded' => 'a&RightVectorBar;b',
                'decoded' => 'a⥓b',
            ],
            1099 => [
                'encoded' => 'a&ring;b',
                'decoded' => 'a˚b',
            ],
            1100 => [
                'encoded' => 'a&rlarr;b',
                'decoded' => 'a⇄b',
            ],
            1101 => [
                'encoded' => 'a&rlhar;b',
                'decoded' => 'a⇌b',
            ],
            1102 => [
                'encoded' => 'a&rlm;b',
                'decoded' => 'a‏b',
            ],
            1103 => [
                'encoded' => 'a&rmoust;b',
                'decoded' => 'a⎱b',
            ],
            1104 => [
                'encoded' => 'a&rnmid;b',
                'decoded' => 'a⫮b',
            ],
            1105 => [
                'encoded' => 'a&roang;b',
                'decoded' => 'a⟭b',
            ],
            1106 => [
                'encoded' => 'a&roarr;b',
                'decoded' => 'a⇾b',
            ],
            1107 => [
                'encoded' => 'a&robrk;b',
                'decoded' => 'a⟧b',
            ],
            1108 => [
                'encoded' => 'a&ropar;b',
                'decoded' => 'a⦆b',
            ],
            1109 => [
                'encoded' => 'a&ropf;b',
                'decoded' => 'a𝕣b',
            ],
            1110 => [
                'encoded' => 'a&Ropf;b',
                'decoded' => 'aℝb',
            ],
            1111 => [
                'encoded' => 'a&roplus;b',
                'decoded' => 'a⨮b',
            ],
            1112 => [
                'encoded' => 'a&rotimes;b',
                'decoded' => 'a⨵b',
            ],
            1113 => [
                'encoded' => 'a&RoundImplies;b',
                'decoded' => 'a⥰b',
            ],
            1114 => [
                'encoded' => 'a&rpargt;b',
                'decoded' => 'a⦔b',
            ],
            1115 => [
                'encoded' => 'a&rppolint;b',
                'decoded' => 'a⨒b',
            ],
            1116 => [
                'encoded' => 'a&rrarr;b',
                'decoded' => 'a⇉b',
            ],
            1117 => [
                'encoded' => 'a&rsaquo;b',
                'decoded' => 'a›b',
            ],
            1118 => [
                'encoded' => 'a&rscr;b',
                'decoded' => 'a𝓇b',
            ],
            1119 => [
                'encoded' => 'a&Rscr;b',
                'decoded' => 'aℛb',
            ],
            1120 => [
                'encoded' => 'a&rsh;b',
                'decoded' => 'a↱b',
            ],
            1121 => [
                'encoded' => 'a&rsquo;b',
                'decoded' => 'a’b',
            ],
            1122 => [
                'encoded' => 'a&rthree;b',
                'decoded' => 'a⋌b',
            ],
            1123 => [
                'encoded' => 'a&rtimes;b',
                'decoded' => 'a⋊b',
            ],
            1124 => [
                'encoded' => 'a&rtri;b',
                'decoded' => 'a▹b',
            ],
            1125 => [
                'encoded' => 'a&rtrie;b',
                'decoded' => 'a⊵b',
            ],
            1126 => [
                'encoded' => 'a&rtrif;b',
                'decoded' => 'a▸b',
            ],
            1127 => [
                'encoded' => 'a&rtriltri;b',
                'decoded' => 'a⧎b',
            ],
            1128 => [
                'encoded' => 'a&RuleDelayed;b',
                'decoded' => 'a⧴b',
            ],
            1129 => [
                'encoded' => 'a&ruluhar;b',
                'decoded' => 'a⥨b',
            ],
            1130 => [
                'encoded' => 'a&rx;b',
                'decoded' => 'a℞b',
            ],
            1131 => [
                'encoded' => 'a&Sacute;b',
                'decoded' => 'aŚb',
            ],
            1132 => [
                'encoded' => 'a&sacute;b',
                'decoded' => 'aśb',
            ],
            1133 => [
                'encoded' => 'a&sbquo;b',
                'decoded' => 'a‚b',
            ],
            1134 => [
                'encoded' => 'a&scap;b',
                'decoded' => 'a⪸b',
            ],
            1135 => [
                'encoded' => 'a&Scaron;b',
                'decoded' => 'aŠb',
            ],
            1136 => [
                'encoded' => 'a&scaron;b',
                'decoded' => 'ašb',
            ],
            1137 => [
                'encoded' => 'a&Sc;b',
                'decoded' => 'a⪼b',
            ],
            1138 => [
                'encoded' => 'a&sc;b',
                'decoded' => 'a≻b',
            ],
            1139 => [
                'encoded' => 'a&sccue;b',
                'decoded' => 'a≽b',
            ],
            1140 => [
                'encoded' => 'a&sce;b',
                'decoded' => 'a⪰b',
            ],
            1141 => [
                'encoded' => 'a&scE;b',
                'decoded' => 'a⪴b',
            ],
            1142 => [
                'encoded' => 'a&Scedil;b',
                'decoded' => 'aŞb',
            ],
            1143 => [
                'encoded' => 'a&scedil;b',
                'decoded' => 'aşb',
            ],
            1144 => [
                'encoded' => 'a&Scirc;b',
                'decoded' => 'aŜb',
            ],
            1145 => [
                'encoded' => 'a&scirc;b',
                'decoded' => 'aŝb',
            ],
            1146 => [
                'encoded' => 'a&scnap;b',
                'decoded' => 'a⪺b',
            ],
            1147 => [
                'encoded' => 'a&scnE;b',
                'decoded' => 'a⪶b',
            ],
            1148 => [
                'encoded' => 'a&scnsim;b',
                'decoded' => 'a⋩b',
            ],
            1149 => [
                'encoded' => 'a&scpolint;b',
                'decoded' => 'a⨓b',
            ],
            1150 => [
                'encoded' => 'a&scsim;b',
                'decoded' => 'a≿b',
            ],
            1151 => [
                'encoded' => 'a&Scy;b',
                'decoded' => 'aСb',
            ],
            1152 => [
                'encoded' => 'a&scy;b',
                'decoded' => 'aсb',
            ],
            1153 => [
                'encoded' => 'a&sdotb;b',
                'decoded' => 'a⊡b',
            ],
            1154 => [
                'encoded' => 'a&sdot;b',
                'decoded' => 'a⋅b',
            ],
            1155 => [
                'encoded' => 'a&sdote;b',
                'decoded' => 'a⩦b',
            ],
            1156 => [
                'encoded' => 'a&searhk;b',
                'decoded' => 'a⤥b',
            ],
            1157 => [
                'encoded' => 'a&searr;b',
                'decoded' => 'a↘b',
            ],
            1158 => [
                'encoded' => 'a&seArr;b',
                'decoded' => 'a⇘b',
            ],
            1159 => [
                'encoded' => 'a&sect;b',
                'decoded' => 'a§b',
            ],
            1160 => [
                'encoded' => 'a&setmn;b',
                'decoded' => 'a∖b',
            ],
            1161 => [
                'encoded' => 'a&sext;b',
                'decoded' => 'a✶b',
            ],
            1162 => [
                'encoded' => 'a&Sfr;b',
                'decoded' => 'a𝔖b',
            ],
            1163 => [
                'encoded' => 'a&sfr;b',
                'decoded' => 'a𝔰b',
            ],
            1164 => [
                'encoded' => 'a&sharp;b',
                'decoded' => 'a♯b',
            ],
            1165 => [
                'encoded' => 'a&SHCHcy;b',
                'decoded' => 'aЩb',
            ],
            1166 => [
                'encoded' => 'a&shchcy;b',
                'decoded' => 'aщb',
            ],
            1167 => [
                'encoded' => 'a&SHcy;b',
                'decoded' => 'aШb',
            ],
            1168 => [
                'encoded' => 'a&shcy;b',
                'decoded' => 'aшb',
            ],
            1169 => [
                'encoded' => 'a&shy;b',
                'decoded' => 'a­b',
            ],
            1170 => [
                'encoded' => 'a&Sigma;b',
                'decoded' => 'aΣb',
            ],
            1171 => [
                'encoded' => 'a&sigma;b',
                'decoded' => 'aσb',
            ],
            1172 => [
                'encoded' => 'a&sigmaf;b',
                'decoded' => 'aςb',
            ],
            1173 => [
                'encoded' => 'a&sim;b',
                'decoded' => 'a∼b',
            ],
            1174 => [
                'encoded' => 'a&simdot;b',
                'decoded' => 'a⩪b',
            ],
            1175 => [
                'encoded' => 'a&sime;b',
                'decoded' => 'a≃b',
            ],
            1176 => [
                'encoded' => 'a&simg;b',
                'decoded' => 'a⪞b',
            ],
            1177 => [
                'encoded' => 'a&simgE;b',
                'decoded' => 'a⪠b',
            ],
            1178 => [
                'encoded' => 'a&siml;b',
                'decoded' => 'a⪝b',
            ],
            1179 => [
                'encoded' => 'a&simlE;b',
                'decoded' => 'a⪟b',
            ],
            1180 => [
                'encoded' => 'a&simne;b',
                'decoded' => 'a≆b',
            ],
            1181 => [
                'encoded' => 'a&simplus;b',
                'decoded' => 'a⨤b',
            ],
            1182 => [
                'encoded' => 'a&simrarr;b',
                'decoded' => 'a⥲b',
            ],
            1183 => [
                'encoded' => 'a&smashp;b',
                'decoded' => 'a⨳b',
            ],
            1184 => [
                'encoded' => 'a&smeparsl;b',
                'decoded' => 'a⧤b',
            ],
            1185 => [
                'encoded' => 'a&smile;b',
                'decoded' => 'a⌣b',
            ],
            1186 => [
                'encoded' => 'a&smt;b',
                'decoded' => 'a⪪b',
            ],
            1187 => [
                'encoded' => 'a&smte;b',
                'decoded' => 'a⪬b',
            ],
            1188 => [
                'encoded' => 'a&smtes;b',
                'decoded' => 'a⪬︀b',
            ],
            1189 => [
                'encoded' => 'a&SOFTcy;b',
                'decoded' => 'aЬb',
            ],
            1190 => [
                'encoded' => 'a&softcy;b',
                'decoded' => 'aьb',
            ],
            1191 => [
                'encoded' => 'a&solbar;b',
                'decoded' => 'a⌿b',
            ],
            1192 => [
                'encoded' => 'a&solb;b',
                'decoded' => 'a⧄b',
            ],
            1193 => [
                'encoded' => 'a&Sopf;b',
                'decoded' => 'a𝕊b',
            ],
            1194 => [
                'encoded' => 'a&sopf;b',
                'decoded' => 'a𝕤b',
            ],
            1195 => [
                'encoded' => 'a&spades;b',
                'decoded' => 'a♠b',
            ],
            1196 => [
                'encoded' => 'a&sqcap;b',
                'decoded' => 'a⊓b',
            ],
            1197 => [
                'encoded' => 'a&sqcaps;b',
                'decoded' => 'a⊓︀b',
            ],
            1198 => [
                'encoded' => 'a&sqcup;b',
                'decoded' => 'a⊔b',
            ],
            1199 => [
                'encoded' => 'a&sqcups;b',
                'decoded' => 'a⊔︀b',
            ],
            1200 => [
                'encoded' => 'a&Sqrt;b',
                'decoded' => 'a√b',
            ],
            1201 => [
                'encoded' => 'a&sqsub;b',
                'decoded' => 'a⊏b',
            ],
            1202 => [
                'encoded' => 'a&sqsube;b',
                'decoded' => 'a⊑b',
            ],
            1203 => [
                'encoded' => 'a&sqsup;b',
                'decoded' => 'a⊐b',
            ],
            1204 => [
                'encoded' => 'a&sqsupe;b',
                'decoded' => 'a⊒b',
            ],
            1205 => [
                'encoded' => 'a&squ;b',
                'decoded' => 'a□b',
            ],
            1206 => [
                'encoded' => 'a&squf;b',
                'decoded' => 'a▪b',
            ],
            1207 => [
                'encoded' => 'a&Sscr;b',
                'decoded' => 'a𝒮b',
            ],
            1208 => [
                'encoded' => 'a&sscr;b',
                'decoded' => 'a𝓈b',
            ],
            1209 => [
                'encoded' => 'a&Star;b',
                'decoded' => 'a⋆b',
            ],
            1210 => [
                'encoded' => 'a&star;b',
                'decoded' => 'a☆b',
            ],
            1211 => [
                'encoded' => 'a&starf;b',
                'decoded' => 'a★b',
            ],
            1212 => [
                'encoded' => 'a&sub;b',
                'decoded' => 'a⊂b',
            ],
            1213 => [
                'encoded' => 'a&Sub;b',
                'decoded' => 'a⋐b',
            ],
            1214 => [
                'encoded' => 'a&subdot;b',
                'decoded' => 'a⪽b',
            ],
            1215 => [
                'encoded' => 'a&subE;b',
                'decoded' => 'a⫅b',
            ],
            1216 => [
                'encoded' => 'a&sube;b',
                'decoded' => 'a⊆b',
            ],
            1217 => [
                'encoded' => 'a&subedot;b',
                'decoded' => 'a⫃b',
            ],
            1218 => [
                'encoded' => 'a&submult;b',
                'decoded' => 'a⫁b',
            ],
            1219 => [
                'encoded' => 'a&subnE;b',
                'decoded' => 'a⫋b',
            ],
            1220 => [
                'encoded' => 'a&subne;b',
                'decoded' => 'a⊊b',
            ],
            1221 => [
                'encoded' => 'a&subplus;b',
                'decoded' => 'a⪿b',
            ],
            1222 => [
                'encoded' => 'a&subrarr;b',
                'decoded' => 'a⥹b',
            ],
            1223 => [
                'encoded' => 'a&subsim;b',
                'decoded' => 'a⫇b',
            ],
            1224 => [
                'encoded' => 'a&subsub;b',
                'decoded' => 'a⫕b',
            ],
            1225 => [
                'encoded' => 'a&subsup;b',
                'decoded' => 'a⫓b',
            ],
            1226 => [
                'encoded' => 'a&sum;b',
                'decoded' => 'a∑b',
            ],
            1227 => [
                'encoded' => 'a&sung;b',
                'decoded' => 'a♪b',
            ],
            1228 => [
                'encoded' => 'a&sup1;b',
                'decoded' => 'a¹b',
            ],
            1229 => [
                'encoded' => 'a&sup2;b',
                'decoded' => 'a²b',
            ],
            1230 => [
                'encoded' => 'a&sup3;b',
                'decoded' => 'a³b',
            ],
            1231 => [
                'encoded' => 'a&sup;b',
                'decoded' => 'a⊃b',
            ],
            1232 => [
                'encoded' => 'a&Sup;b',
                'decoded' => 'a⋑b',
            ],
            1233 => [
                'encoded' => 'a&supdot;b',
                'decoded' => 'a⪾b',
            ],
            1234 => [
                'encoded' => 'a&supdsub;b',
                'decoded' => 'a⫘b',
            ],
            1235 => [
                'encoded' => 'a&supE;b',
                'decoded' => 'a⫆b',
            ],
            1236 => [
                'encoded' => 'a&supe;b',
                'decoded' => 'a⊇b',
            ],
            1237 => [
                'encoded' => 'a&supedot;b',
                'decoded' => 'a⫄b',
            ],
            1238 => [
                'encoded' => 'a&suphsol;b',
                'decoded' => 'a⟉b',
            ],
            1239 => [
                'encoded' => 'a&suphsub;b',
                'decoded' => 'a⫗b',
            ],
            1240 => [
                'encoded' => 'a&suplarr;b',
                'decoded' => 'a⥻b',
            ],
            1241 => [
                'encoded' => 'a&supmult;b',
                'decoded' => 'a⫂b',
            ],
            1242 => [
                'encoded' => 'a&supnE;b',
                'decoded' => 'a⫌b',
            ],
            1243 => [
                'encoded' => 'a&supne;b',
                'decoded' => 'a⊋b',
            ],
            1244 => [
                'encoded' => 'a&supplus;b',
                'decoded' => 'a⫀b',
            ],
            1245 => [
                'encoded' => 'a&supsim;b',
                'decoded' => 'a⫈b',
            ],
            1246 => [
                'encoded' => 'a&supsub;b',
                'decoded' => 'a⫔b',
            ],
            1247 => [
                'encoded' => 'a&supsup;b',
                'decoded' => 'a⫖b',
            ],
            1248 => [
                'encoded' => 'a&swarhk;b',
                'decoded' => 'a⤦b',
            ],
            1249 => [
                'encoded' => 'a&swarr;b',
                'decoded' => 'a↙b',
            ],
            1250 => [
                'encoded' => 'a&swArr;b',
                'decoded' => 'a⇙b',
            ],
            1251 => [
                'encoded' => 'a&swnwar;b',
                'decoded' => 'a⤪b',
            ],
            1252 => [
                'encoded' => 'a&szlig;b',
                'decoded' => 'aßb',
            ],
            1253 => [
                'encoded' => 'a&target;b',
                'decoded' => 'a⌖b',
            ],
            1254 => [
                'encoded' => 'a&Tau;b',
                'decoded' => 'aΤb',
            ],
            1255 => [
                'encoded' => 'a&tau;b',
                'decoded' => 'aτb',
            ],
            1256 => [
                'encoded' => 'a&tbrk;b',
                'decoded' => 'a⎴b',
            ],
            1257 => [
                'encoded' => 'a&Tcaron;b',
                'decoded' => 'aŤb',
            ],
            1258 => [
                'encoded' => 'a&tcaron;b',
                'decoded' => 'aťb',
            ],
            1259 => [
                'encoded' => 'a&Tcedil;b',
                'decoded' => 'aŢb',
            ],
            1260 => [
                'encoded' => 'a&tcedil;b',
                'decoded' => 'aţb',
            ],
            1261 => [
                'encoded' => 'a&Tcy;b',
                'decoded' => 'aТb',
            ],
            1262 => [
                'encoded' => 'a&tcy;b',
                'decoded' => 'aтb',
            ],
            1263 => [
                'encoded' => 'a&tdot;b',
                'decoded' => 'a⃛b',
            ],
            1264 => [
                'encoded' => 'a&telrec;b',
                'decoded' => 'a⌕b',
            ],
            1265 => [
                'encoded' => 'a&Tfr;b',
                'decoded' => 'a𝔗b',
            ],
            1266 => [
                'encoded' => 'a&tfr;b',
                'decoded' => 'a𝔱b',
            ],
            1267 => [
                'encoded' => 'a&there4;b',
                'decoded' => 'a∴b',
            ],
            1268 => [
                'encoded' => 'a&Theta;b',
                'decoded' => 'aΘb',
            ],
            1269 => [
                'encoded' => 'a&theta;b',
                'decoded' => 'aθb',
            ],
            1270 => [
                'encoded' => 'a&thetav;b',
                'decoded' => 'aϑb',
            ],
            1271 => [
                'encoded' => 'a&ThickSpace;b',
                'decoded' => 'a  b',
            ],
            1272 => [
                'encoded' => 'a&thinsp;b',
                'decoded' => 'a b',
            ],
            1273 => [
                'encoded' => 'a&THORN;b',
                'decoded' => 'aÞb',
            ],
            1274 => [
                'encoded' => 'a&thorn;b',
                'decoded' => 'aþb',
            ],
            1275 => [
                'encoded' => 'a&tilde;b',
                'decoded' => 'a˜b',
            ],
            1276 => [
                'encoded' => 'a&timesbar;b',
                'decoded' => 'a⨱b',
            ],
            1277 => [
                'encoded' => 'a&timesb;b',
                'decoded' => 'a⊠b',
            ],
            1278 => [
                'encoded' => 'a&times;b',
                'decoded' => 'a×b',
            ],
            1279 => [
                'encoded' => 'a&timesd;b',
                'decoded' => 'a⨰b',
            ],
            1280 => [
                'encoded' => 'a&tint;b',
                'decoded' => 'a∭b',
            ],
            1281 => [
                'encoded' => 'a&toea;b',
                'decoded' => 'a⤨b',
            ],
            1282 => [
                'encoded' => 'a&topbot;b',
                'decoded' => 'a⌶b',
            ],
            1283 => [
                'encoded' => 'a&topcir;b',
                'decoded' => 'a⫱b',
            ],
            1284 => [
                'encoded' => 'a&top;b',
                'decoded' => 'a⊤b',
            ],
            1285 => [
                'encoded' => 'a&Topf;b',
                'decoded' => 'a𝕋b',
            ],
            1286 => [
                'encoded' => 'a&topf;b',
                'decoded' => 'a𝕥b',
            ],
            1287 => [
                'encoded' => 'a&topfork;b',
                'decoded' => 'a⫚b',
            ],
            1288 => [
                'encoded' => 'a&tosa;b',
                'decoded' => 'a⤩b',
            ],
            1289 => [
                'encoded' => 'a&tprime;b',
                'decoded' => 'a‴b',
            ],
            1290 => [
                'encoded' => 'a&trade;b',
                'decoded' => 'a™b',
            ],
            1291 => [
                'encoded' => 'a&tridot;b',
                'decoded' => 'a◬b',
            ],
            1292 => [
                'encoded' => 'a&trie;b',
                'decoded' => 'a≜b',
            ],
            1293 => [
                'encoded' => 'a&triminus;b',
                'decoded' => 'a⨺b',
            ],
            1294 => [
                'encoded' => 'a&triplus;b',
                'decoded' => 'a⨹b',
            ],
            1295 => [
                'encoded' => 'a&trisb;b',
                'decoded' => 'a⧍b',
            ],
            1296 => [
                'encoded' => 'a&tritime;b',
                'decoded' => 'a⨻b',
            ],
            1297 => [
                'encoded' => 'a&trpezium;b',
                'decoded' => 'a⏢b',
            ],
            1298 => [
                'encoded' => 'a&Tscr;b',
                'decoded' => 'a𝒯b',
            ],
            1299 => [
                'encoded' => 'a&tscr;b',
                'decoded' => 'a𝓉b',
            ],
            1300 => [
                'encoded' => 'a&TScy;b',
                'decoded' => 'aЦb',
            ],
            1301 => [
                'encoded' => 'a&tscy;b',
                'decoded' => 'aцb',
            ],
            1302 => [
                'encoded' => 'a&TSHcy;b',
                'decoded' => 'aЋb',
            ],
            1303 => [
                'encoded' => 'a&tshcy;b',
                'decoded' => 'aћb',
            ],
            1304 => [
                'encoded' => 'a&Tstrok;b',
                'decoded' => 'aŦb',
            ],
            1305 => [
                'encoded' => 'a&tstrok;b',
                'decoded' => 'aŧb',
            ],
            1306 => [
                'encoded' => 'a&twixt;b',
                'decoded' => 'a≬b',
            ],
            1307 => [
                'encoded' => 'a&Uacute;b',
                'decoded' => 'aÚb',
            ],
            1308 => [
                'encoded' => 'a&uacute;b',
                'decoded' => 'aúb',
            ],
            1309 => [
                'encoded' => 'a&uarr;b',
                'decoded' => 'a↑b',
            ],
            1310 => [
                'encoded' => 'a&Uarr;b',
                'decoded' => 'a↟b',
            ],
            1311 => [
                'encoded' => 'a&uArr;b',
                'decoded' => 'a⇑b',
            ],
            1312 => [
                'encoded' => 'a&Uarrocir;b',
                'decoded' => 'a⥉b',
            ],
            1313 => [
                'encoded' => 'a&Ubrcy;b',
                'decoded' => 'aЎb',
            ],
            1314 => [
                'encoded' => 'a&ubrcy;b',
                'decoded' => 'aўb',
            ],
            1315 => [
                'encoded' => 'a&Ubreve;b',
                'decoded' => 'aŬb',
            ],
            1316 => [
                'encoded' => 'a&ubreve;b',
                'decoded' => 'aŭb',
            ],
            1317 => [
                'encoded' => 'a&Ucirc;b',
                'decoded' => 'aÛb',
            ],
            1318 => [
                'encoded' => 'a&ucirc;b',
                'decoded' => 'aûb',
            ],
            1319 => [
                'encoded' => 'a&Ucy;b',
                'decoded' => 'aУb',
            ],
            1320 => [
                'encoded' => 'a&ucy;b',
                'decoded' => 'aуb',
            ],
            1321 => [
                'encoded' => 'a&udarr;b',
                'decoded' => 'a⇅b',
            ],
            1322 => [
                'encoded' => 'a&Udblac;b',
                'decoded' => 'aŰb',
            ],
            1323 => [
                'encoded' => 'a&udblac;b',
                'decoded' => 'aűb',
            ],
            1324 => [
                'encoded' => 'a&udhar;b',
                'decoded' => 'a⥮b',
            ],
            1325 => [
                'encoded' => 'a&ufisht;b',
                'decoded' => 'a⥾b',
            ],
            1326 => [
                'encoded' => 'a&Ufr;b',
                'decoded' => 'a𝔘b',
            ],
            1327 => [
                'encoded' => 'a&ufr;b',
                'decoded' => 'a𝔲b',
            ],
            1328 => [
                'encoded' => 'a&Ugrave;b',
                'decoded' => 'aÙb',
            ],
            1329 => [
                'encoded' => 'a&ugrave;b',
                'decoded' => 'aùb',
            ],
            1330 => [
                'encoded' => 'a&uHar;b',
                'decoded' => 'a⥣b',
            ],
            1331 => [
                'encoded' => 'a&uharl;b',
                'decoded' => 'a↿b',
            ],
            1332 => [
                'encoded' => 'a&uharr;b',
                'decoded' => 'a↾b',
            ],
            1333 => [
                'encoded' => 'a&uhblk;b',
                'decoded' => 'a▀b',
            ],
            1334 => [
                'encoded' => 'a&ulcorn;b',
                'decoded' => 'a⌜b',
            ],
            1335 => [
                'encoded' => 'a&ulcrop;b',
                'decoded' => 'a⌏b',
            ],
            1336 => [
                'encoded' => 'a&ultri;b',
                'decoded' => 'a◸b',
            ],
            1337 => [
                'encoded' => 'a&Umacr;b',
                'decoded' => 'aŪb',
            ],
            1338 => [
                'encoded' => 'a&umacr;b',
                'decoded' => 'aūb',
            ],
            1339 => [
                'encoded' => 'a&UnderBrace;b',
                'decoded' => 'a⏟b',
            ],
            1340 => [
                'encoded' => 'a&UnderParenthesis;b',
                'decoded' => 'a⏝b',
            ],
            1341 => [
                'encoded' => 'a&Uogon;b',
                'decoded' => 'aŲb',
            ],
            1342 => [
                'encoded' => 'a&uogon;b',
                'decoded' => 'aųb',
            ],
            1343 => [
                'encoded' => 'a&Uopf;b',
                'decoded' => 'a𝕌b',
            ],
            1344 => [
                'encoded' => 'a&uopf;b',
                'decoded' => 'a𝕦b',
            ],
            1345 => [
                'encoded' => 'a&UpArrowBar;b',
                'decoded' => 'a⤒b',
            ],
            1346 => [
                'encoded' => 'a&uplus;b',
                'decoded' => 'a⊎b',
            ],
            1347 => [
                'encoded' => 'a&upsi;b',
                'decoded' => 'aυb',
            ],
            1348 => [
                'encoded' => 'a&Upsi;b',
                'decoded' => 'aϒb',
            ],
            1349 => [
                'encoded' => 'a&Upsilon;b',
                'decoded' => 'aΥb',
            ],
            1350 => [
                'encoded' => 'a&urcorn;b',
                'decoded' => 'a⌝b',
            ],
            1351 => [
                'encoded' => 'a&urcrop;b',
                'decoded' => 'a⌎b',
            ],
            1352 => [
                'encoded' => 'a&Uring;b',
                'decoded' => 'aŮb',
            ],
            1353 => [
                'encoded' => 'a&uring;b',
                'decoded' => 'aůb',
            ],
            1354 => [
                'encoded' => 'a&urtri;b',
                'decoded' => 'a◹b',
            ],
            1355 => [
                'encoded' => 'a&Uscr;b',
                'decoded' => 'a𝒰b',
            ],
            1356 => [
                'encoded' => 'a&uscr;b',
                'decoded' => 'a𝓊b',
            ],
            1357 => [
                'encoded' => 'a&utdot;b',
                'decoded' => 'a⋰b',
            ],
            1358 => [
                'encoded' => 'a&Utilde;b',
                'decoded' => 'aŨb',
            ],
            1359 => [
                'encoded' => 'a&utilde;b',
                'decoded' => 'aũb',
            ],
            1360 => [
                'encoded' => 'a&utri;b',
                'decoded' => 'a▵b',
            ],
            1361 => [
                'encoded' => 'a&utrif;b',
                'decoded' => 'a▴b',
            ],
            1362 => [
                'encoded' => 'a&uuarr;b',
                'decoded' => 'a⇈b',
            ],
            1363 => [
                'encoded' => 'a&Uuml;b',
                'decoded' => 'aÜb',
            ],
            1364 => [
                'encoded' => 'a&uuml;b',
                'decoded' => 'aüb',
            ],
            1365 => [
                'encoded' => 'a&uwangle;b',
                'decoded' => 'a⦧b',
            ],
            1366 => [
                'encoded' => 'a&vangrt;b',
                'decoded' => 'a⦜b',
            ],
            1367 => [
                'encoded' => 'a&varr;b',
                'decoded' => 'a↕b',
            ],
            1368 => [
                'encoded' => 'a&vArr;b',
                'decoded' => 'a⇕b',
            ],
            1369 => [
                'encoded' => 'a&vBar;b',
                'decoded' => 'a⫨b',
            ],
            1370 => [
                'encoded' => 'a&Vbar;b',
                'decoded' => 'a⫫b',
            ],
            1371 => [
                'encoded' => 'a&vBarv;b',
                'decoded' => 'a⫩b',
            ],
            1372 => [
                'encoded' => 'a&Vcy;b',
                'decoded' => 'aВb',
            ],
            1373 => [
                'encoded' => 'a&vcy;b',
                'decoded' => 'aвb',
            ],
            1374 => [
                'encoded' => 'a&vdash;b',
                'decoded' => 'a⊢b',
            ],
            1375 => [
                'encoded' => 'a&vDash;b',
                'decoded' => 'a⊨b',
            ],
            1376 => [
                'encoded' => 'a&Vdash;b',
                'decoded' => 'a⊩b',
            ],
            1377 => [
                'encoded' => 'a&VDash;b',
                'decoded' => 'a⊫b',
            ],
            1378 => [
                'encoded' => 'a&Vdashl;b',
                'decoded' => 'a⫦b',
            ],
            1379 => [
                'encoded' => 'a&veebar;b',
                'decoded' => 'a⊻b',
            ],
            1380 => [
                'encoded' => 'a&Vee;b',
                'decoded' => 'a⋁b',
            ],
            1381 => [
                'encoded' => 'a&veeeq;b',
                'decoded' => 'a≚b',
            ],
            1382 => [
                'encoded' => 'a&vellip;b',
                'decoded' => 'a⋮b',
            ],
            1383 => [
                'encoded' => 'a&Vert;b',
                'decoded' => 'a‖b',
            ],
            1384 => [
                'encoded' => 'a&VerticalSeparator;b',
                'decoded' => 'a❘b',
            ],
            1385 => [
                'encoded' => 'a&Vfr;b',
                'decoded' => 'a𝔙b',
            ],
            1386 => [
                'encoded' => 'a&vfr;b',
                'decoded' => 'a𝔳b',
            ],
            1387 => [
                'encoded' => 'a&vltri;b',
                'decoded' => 'a⊲b',
            ],
            1388 => [
                'encoded' => 'a&vnsub;b',
                'decoded' => 'a⊂⃒b',
            ],
            1389 => [
                'encoded' => 'a&vnsup;b',
                'decoded' => 'a⊃⃒b',
            ],
            1390 => [
                'encoded' => 'a&Vopf;b',
                'decoded' => 'a𝕍b',
            ],
            1391 => [
                'encoded' => 'a&vopf;b',
                'decoded' => 'a𝕧b',
            ],
            1392 => [
                'encoded' => 'a&vrtri;b',
                'decoded' => 'a⊳b',
            ],
            1393 => [
                'encoded' => 'a&Vscr;b',
                'decoded' => 'a𝒱b',
            ],
            1394 => [
                'encoded' => 'a&vscr;b',
                'decoded' => 'a𝓋b',
            ],
            1395 => [
                'encoded' => 'a&vsubnE;b',
                'decoded' => 'a⫋︀b',
            ],
            1396 => [
                'encoded' => 'a&vsubne;b',
                'decoded' => 'a⊊︀b',
            ],
            1397 => [
                'encoded' => 'a&vsupnE;b',
                'decoded' => 'a⫌︀b',
            ],
            1398 => [
                'encoded' => 'a&vsupne;b',
                'decoded' => 'a⊋︀b',
            ],
            1399 => [
                'encoded' => 'a&Vvdash;b',
                'decoded' => 'a⊪b',
            ],
            1400 => [
                'encoded' => 'a&vzigzag;b',
                'decoded' => 'a⦚b',
            ],
            1401 => [
                'encoded' => 'a&Wcirc;b',
                'decoded' => 'aŴb',
            ],
            1402 => [
                'encoded' => 'a&wcirc;b',
                'decoded' => 'aŵb',
            ],
            1403 => [
                'encoded' => 'a&wedbar;b',
                'decoded' => 'a⩟b',
            ],
            1404 => [
                'encoded' => 'a&Wedge;b',
                'decoded' => 'a⋀b',
            ],
            1405 => [
                'encoded' => 'a&wedgeq;b',
                'decoded' => 'a≙b',
            ],
            1406 => [
                'encoded' => 'a&Wfr;b',
                'decoded' => 'a𝔚b',
            ],
            1407 => [
                'encoded' => 'a&wfr;b',
                'decoded' => 'a𝔴b',
            ],
            1408 => [
                'encoded' => 'a&Wopf;b',
                'decoded' => 'a𝕎b',
            ],
            1409 => [
                'encoded' => 'a&wopf;b',
                'decoded' => 'a𝕨b',
            ],
            1410 => [
                'encoded' => 'a&wp;b',
                'decoded' => 'a℘b',
            ],
            1411 => [
                'encoded' => 'a&wr;b',
                'decoded' => 'a≀b',
            ],
            1412 => [
                'encoded' => 'a&Wscr;b',
                'decoded' => 'a𝒲b',
            ],
            1413 => [
                'encoded' => 'a&wscr;b',
                'decoded' => 'a𝓌b',
            ],
            1414 => [
                'encoded' => 'a&xcap;b',
                'decoded' => 'a⋂b',
            ],
            1415 => [
                'encoded' => 'a&xcirc;b',
                'decoded' => 'a◯b',
            ],
            1416 => [
                'encoded' => 'a&xcup;b',
                'decoded' => 'a⋃b',
            ],
            1417 => [
                'encoded' => 'a&xdtri;b',
                'decoded' => 'a▽b',
            ],
            1418 => [
                'encoded' => 'a&Xfr;b',
                'decoded' => 'a𝔛b',
            ],
            1419 => [
                'encoded' => 'a&xfr;b',
                'decoded' => 'a𝔵b',
            ],
            1420 => [
                'encoded' => 'a&xharr;b',
                'decoded' => 'a⟷b',
            ],
            1421 => [
                'encoded' => 'a&xhArr;b',
                'decoded' => 'a⟺b',
            ],
            1422 => [
                'encoded' => 'a&Xi;b',
                'decoded' => 'aΞb',
            ],
            1423 => [
                'encoded' => 'a&xi;b',
                'decoded' => 'aξb',
            ],
            1424 => [
                'encoded' => 'a&xlarr;b',
                'decoded' => 'a⟵b',
            ],
            1425 => [
                'encoded' => 'a&xlArr;b',
                'decoded' => 'a⟸b',
            ],
            1426 => [
                'encoded' => 'a&xmap;b',
                'decoded' => 'a⟼b',
            ],
            1427 => [
                'encoded' => 'a&xnis;b',
                'decoded' => 'a⋻b',
            ],
            1428 => [
                'encoded' => 'a&xodot;b',
                'decoded' => 'a⨀b',
            ],
            1429 => [
                'encoded' => 'a&Xopf;b',
                'decoded' => 'a𝕏b',
            ],
            1430 => [
                'encoded' => 'a&xopf;b',
                'decoded' => 'a𝕩b',
            ],
            1431 => [
                'encoded' => 'a&xoplus;b',
                'decoded' => 'a⨁b',
            ],
            1432 => [
                'encoded' => 'a&xotime;b',
                'decoded' => 'a⨂b',
            ],
            1433 => [
                'encoded' => 'a&xrarr;b',
                'decoded' => 'a⟶b',
            ],
            1434 => [
                'encoded' => 'a&xrArr;b',
                'decoded' => 'a⟹b',
            ],
            1435 => [
                'encoded' => 'a&Xscr;b',
                'decoded' => 'a𝒳b',
            ],
            1436 => [
                'encoded' => 'a&xscr;b',
                'decoded' => 'a𝓍b',
            ],
            1437 => [
                'encoded' => 'a&xsqcup;b',
                'decoded' => 'a⨆b',
            ],
            1438 => [
                'encoded' => 'a&xuplus;b',
                'decoded' => 'a⨄b',
            ],
            1439 => [
                'encoded' => 'a&xutri;b',
                'decoded' => 'a△b',
            ],
            1440 => [
                'encoded' => 'a&Yacute;b',
                'decoded' => 'aÝb',
            ],
            1441 => [
                'encoded' => 'a&yacute;b',
                'decoded' => 'aýb',
            ],
            1442 => [
                'encoded' => 'a&YAcy;b',
                'decoded' => 'aЯb',
            ],
            1443 => [
                'encoded' => 'a&yacy;b',
                'decoded' => 'aяb',
            ],
            1444 => [
                'encoded' => 'a&Ycirc;b',
                'decoded' => 'aŶb',
            ],
            1445 => [
                'encoded' => 'a&ycirc;b',
                'decoded' => 'aŷb',
            ],
            1446 => [
                'encoded' => 'a&Ycy;b',
                'decoded' => 'aЫb',
            ],
            1447 => [
                'encoded' => 'a&ycy;b',
                'decoded' => 'aыb',
            ],
            1448 => [
                'encoded' => 'a&yen;b',
                'decoded' => 'a¥b',
            ],
            1449 => [
                'encoded' => 'a&Yfr;b',
                'decoded' => 'a𝔜b',
            ],
            1450 => [
                'encoded' => 'a&yfr;b',
                'decoded' => 'a𝔶b',
            ],
            1451 => [
                'encoded' => 'a&YIcy;b',
                'decoded' => 'aЇb',
            ],
            1452 => [
                'encoded' => 'a&yicy;b',
                'decoded' => 'aїb',
            ],
            1453 => [
                'encoded' => 'a&Yopf;b',
                'decoded' => 'a𝕐b',
            ],
            1454 => [
                'encoded' => 'a&yopf;b',
                'decoded' => 'a𝕪b',
            ],
            1455 => [
                'encoded' => 'a&Yscr;b',
                'decoded' => 'a𝒴b',
            ],
            1456 => [
                'encoded' => 'a&yscr;b',
                'decoded' => 'a𝓎b',
            ],
            1457 => [
                'encoded' => 'a&YUcy;b',
                'decoded' => 'aЮb',
            ],
            1458 => [
                'encoded' => 'a&yucy;b',
                'decoded' => 'aюb',
            ],
            1459 => [
                'encoded' => 'a&yuml;b',
                'decoded' => 'aÿb',
            ],
            1460 => [
                'encoded' => 'a&Yuml;b',
                'decoded' => 'aŸb',
            ],
            1461 => [
                'encoded' => 'a&Zacute;b',
                'decoded' => 'aŹb',
            ],
            1462 => [
                'encoded' => 'a&zacute;b',
                'decoded' => 'aźb',
            ],
            1463 => [
                'encoded' => 'a&Zcaron;b',
                'decoded' => 'aŽb',
            ],
            1464 => [
                'encoded' => 'a&zcaron;b',
                'decoded' => 'ažb',
            ],
            1465 => [
                'encoded' => 'a&Zcy;b',
                'decoded' => 'aЗb',
            ],
            1466 => [
                'encoded' => 'a&zcy;b',
                'decoded' => 'aзb',
            ],
            1467 => [
                'encoded' => 'a&Zdot;b',
                'decoded' => 'aŻb',
            ],
            1468 => [
                'encoded' => 'a&zdot;b',
                'decoded' => 'ażb',
            ],
            1469 => [
                'encoded' => 'a&ZeroWidthSpace;b',
                'decoded' => 'ab',
            ],
            1470 => [
                'encoded' => 'a&Zeta;b',
                'decoded' => 'aΖb',
            ],
            1471 => [
                'encoded' => 'a&zeta;b',
                'decoded' => 'aζb',
            ],
            1472 => [
                'encoded' => 'a&zfr;b',
                'decoded' => 'a𝔷b',
            ],
            1473 => [
                'encoded' => 'a&Zfr;b',
                'decoded' => 'aℨb',
            ],
            1474 => [
                'encoded' => 'a&ZHcy;b',
                'decoded' => 'aЖb',
            ],
            1475 => [
                'encoded' => 'a&zhcy;b',
                'decoded' => 'aжb',
            ],
            1476 => [
                'encoded' => 'a&zigrarr;b',
                'decoded' => 'a⇝b',
            ],
            1477 => [
                'encoded' => 'a&zopf;b',
                'decoded' => 'a𝕫b',
            ],
            1478 => [
                'encoded' => 'a&Zopf;b',
                'decoded' => 'aℤb',
            ],
            1479 => [
                'encoded' => 'a&Zscr;b',
                'decoded' => 'a𝒵b',
            ],
            1480 => [
                'encoded' => 'a&zscr;b',
                'decoded' => 'a𝓏b',
            ],
            1481 => [
                'encoded' => 'a&zwj;b',
                'decoded' => 'a‍b',
            ],
            1482 => [
                'encoded' => 'a&zwnj;b',
                'decoded' => 'a‌b',
            ],
            1483 => [
                'encoded' => '&amp;xxx; &amp;xxx &amp;thorn; &amp;thorn &amp;curren;t &amp;current',
                'decoded' => '&xxx; &xxx þ &thorn ¤t &current',
            ],
        ];

        foreach ($encodeData as $encodeDataInnerArray) {
            static::assertSame(
                UTF8::to_utf8($encodeDataInnerArray['decoded']),
                UTF8::html_decode($encodeDataInnerArray['encoded']),
                'tested: ' . \print_r($encodeDataInnerArray, true)
            );
        }
    }
}
