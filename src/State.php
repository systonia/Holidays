<?php

namespace Systonia\Holidays;

/** The 16 states of Germany, by their official two-letter codes. */
enum State: string
{
    case BadenWuerttemberg = 'BW';
    case Bavaria = 'BY';
    case Berlin = 'BE';
    case Brandenburg = 'BB';
    case Bremen = 'HB';
    case Hamburg = 'HH';
    case Hesse = 'HE';
    case MecklenburgVorpommern = 'MV';
    case LowerSaxony = 'NI';
    case NorthRhineWestphalia = 'NW';
    case RhinelandPalatinate = 'RP';
    case Saarland = 'SL';
    case Saxony = 'SN';
    case SaxonyAnhalt = 'ST';
    case SchleswigHolstein = 'SH';
    case Thuringia = 'TH';
}
