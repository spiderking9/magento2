<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category        Blackbird
 * @package         Blackbird_ContentManager
 * @copyright       Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author          Blackbird Team
 * @license         https://www.advancedcontentmanager.com/license/
 */

namespace Blackbird\ContentManager\Model\Config\Source;

/**
 * Class Flags
 *
 * @package Blackbird\ContentManager\Model\Config\Source
 */
class Flags implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_flagOptions = [];

    /**
     * Get flags option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!empty($this->_flagOptions)) {
            return $this->_flagOptions;
        }

        $this->_flagOptions[] = [
            'label' => __('-- Please select --'),
            'value' => '_unknown.png',
        ];

        foreach ($this->getFlags() as $flag) {
            $this->_flagOptions[] = [
                'label' => $flag,
                'value' => $flag,
            ];
        }

        return $this->_flagOptions;
    }

    /**
     * List of flags
     *
     * @return array
     */
    protected function getFlags()
    {
        return [
            'AD.png',
            'AE.png',
            'AF.png',
            'AG.png',
            'AI.png',
            'AL.png',
            'AM.png',
            'AN.png',
            'AO.png',
            'AQ.png',
            'AR.png',
            'AS.png',
            'AT.png',
            'AU.png',
            'AW.png',
            'AX.png',
            'AZ.png',
            'BA.png',
            'BB.png',
            'BD.png',
            'BE.png',
            'BF.png',
            'BG.png',
            'BH.png',
            'BI.png',
            'BJ.png',
            'BL.png',
            'BM.png',
            'BN.png',
            'BO.png',
            'BR.png',
            'BS.png',
            'BT.png',
            'BW.png',
            'BY.png',
            'BZ.png',
            'CA.png',
            'CC.png',
            'CD.png',
            'CF.png',
            'CG.png',
            'CH.png',
            'CI.png',
            'CK.png',
            'CL.png',
            'CM.png',
            'CN.png',
            'CO.png',
            'CR.png',
            'CU.png',
            'CV.png',
            'CW.png',
            'CX.png',
            'CY.png',
            'CZ.png',
            'DE.png',
            'DJ.png',
            'DK.png',
            'DM.png',
            'DO.png',
            'DZ.png',
            'EC.png',
            'EE.png',
            'EG.png',
            'EH.png',
            'ER.png',
            'ES.png',
            'ET.png',
            'EU.png',
            'FI.png',
            'FJ.png',
            'FK.png',
            'FM.png',
            'FO.png',
            'FR.png',
            'GA.png',
            'GB.png',
            'GD.png',
            'GE.png',
            'GG.png',
            'GH.png',
            'GI.png',
            'GL.png',
            'GM.png',
            'GN.png',
            'GQ.png',
            'GR.png',
            'GS.png',
            'GT.png',
            'GU.png',
            'GW.png',
            'GY.png',
            'HK.png',
            'HN.png',
            'HR.png',
            'HT.png',
            'HU.png',
            'IC.png',
            'ID.png',
            'IE.png',
            'IL.png',
            'IM.png',
            'IN.png',
            'IQ.png',
            'IR.png',
            'IS.png',
            'IT.png',
            'JE.png',
            'JM.png',
            'JO.png',
            'JP.png',
            'KE.png',
            'KG.png',
            'KH.png',
            'KI.png',
            'KM.png',
            'KN.png',
            'KP.png',
            'KR.png',
            'KW.png',
            'KY.png',
            'KZ.png',
            'LA.png',
            'LB.png',
            'LC.png',
            'LI.png',
            'LK.png',
            'LR.png',
            'LS.png',
            'LT.png',
            'LU.png',
            'LV.png',
            'LY.png',
            'MA.png',
            'MC.png',
            'MD.png',
            'ME.png',
            'MF.png',
            'MG.png',
            'MH.png',
            'MK.png',
            'ML.png',
            'MM.png',
            'MN.png',
            'MO.png',
            'MP.png',
            'MQ.png',
            'MR.png',
            'MS.png',
            'MT.png',
            'MU.png',
            'MV.png',
            'MW.png',
            'MX.png',
            'MY.png',
            'MZ.png',
            'NA.png',
            'NC.png',
            'NE.png',
            'NF.png',
            'NG.png',
            'NI.png',
            'NL.png',
            'NO.png',
            'NP.png',
            'NR.png',
            'NU.png',
            'NZ.png',
            'OM.png',
            'PA.png',
            'PE.png',
            'PF.png',
            'PG.png',
            'PH.png',
            'PK.png',
            'PL.png',
            'PN.png',
            'PR.png',
            'PS.png',
            'PT.png',
            'PW.png',
            'PY.png',
            'QA.png',
            'RO.png',
            'RS.png',
            'RU.png',
            'RW.png',
            'SA.png',
            'SB.png',
            'SC.png',
            'SD.png',
            'SE.png',
            'SG.png',
            'SH.png',
            'SI.png',
            'SK.png',
            'SL.png',
            'SM.png',
            'SN.png',
            'SO.png',
            'SR.png',
            'SS.png',
            'ST.png',
            'SV.png',
            'SY.png',
            'SZ.png',
            'TC.png',
            'TD.png',
            'TF.png',
            'TG.png',
            'TH.png',
            'TJ.png',
            'TK.png',
            'TL.png',
            'TM.png',
            'TN.png',
            'TO.png',
            'TR.png',
            'TT.png',
            'TV.png',
            'TW.png',
            'TZ.png',
            'UA.png',
            'UG.png',
            'US.png',
            'UY.png',
            'UZ.png',
            'VA.png',
            'VC.png',
            'VE.png',
            'VG.png',
            'VI.png',
            'VN.png',
            'VU.png',
            'WF.png',
            'WS.png',
            'YE.png',
            'YT.png',
            'ZA.png',
            'ZM.png',
            'ZW.png',
            '_abkhazia.png',
            '_basque-country.png',
            '_british-antarctic-territory.png',
            '_commonwealth.png',
            '_england.png',
            '_gosquared.png',
            '_kosovo.png',
            '_mars.png',
            '_nagorno-karabakh.png',
            '_nato.png',
            '_northern-cyprus.png',
            '_olympics.png',
            '_red-cross.png',
            '_scotland.png',
            '_somaliland.png',
            '_south-ossetia.png',
            '_united-nations.png',
            '_unknown.png',
            '_wales.png',
            '_world.png',
        ];
    }

    /**
     * Get last part of an url
     *
     * @param string $string
     * @return string
     */
    protected function getFileName($string)
    {
        return substr($string, (strrpos($string, '/') + 1));
    }
}
