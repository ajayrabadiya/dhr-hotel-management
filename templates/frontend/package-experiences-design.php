<?php
/**
 * Fifth Package Design (Bird Packages Experiences) Template
 * Shortcode: [dhr_package_experiences_design]
 */

if (!defined('ABSPATH')) {
    exit;
}

$plugin_url = plugin_dir_url(dirname(__FILE__, 2));
?>

<!-- Five Package Design -->
<div class="bird-packages__experinces">
    <div class="bird-packages__first">
        <div class="bird-packages__experinces__container">
            <div class="bird-packages__tags">
                <div class="bird-packages__tag__card">
                    <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.40297 7.36312C9.43734 7.36312 11.0911 5.70937 11.0911 3.675C11.0911 1.64062 9.43734 0 7.40297 0C5.36859 0 3.71484 1.65375 3.71484 3.68812C3.71484 5.7225 5.36859 7.37625 7.40297 7.37625V7.36312ZM4.69922 3.68812C4.69922 2.205 5.90672 0.9975 7.38984 0.9975C8.87297 0.9975 10.0805 2.205 10.0805 3.68812C10.0805 5.17125 8.87297 6.37875 7.38984 6.37875C5.90672 6.37875 4.69922 5.17125 4.69922 3.68812Z" fill="#062943" />
                        <path d="M16.1836 8.71496C17.9948 8.71496 19.4517 7.24496 19.4517 5.44684C19.4517 3.64871 17.9817 2.17871 16.1836 2.17871C14.3855 2.17871 12.9023 3.64871 12.9023 5.44684C12.9023 7.24496 14.3723 8.71496 16.1836 8.71496ZM13.8998 5.44684C13.8998 4.18684 14.9236 3.16309 16.1836 3.16309C17.4436 3.16309 18.4673 4.18684 18.4673 5.44684C18.4673 6.70684 17.4436 7.73059 16.1836 7.73059C14.9236 7.73059 13.8998 6.70684 13.8998 5.44684Z" fill="#062943" />
                        <path d="M22.365 11.9699L22.3388 11.8912H22.3256C22.1025 11.3531 21.7744 10.8674 21.3544 10.4343C20.9213 9.98807 20.4094 9.64682 19.845 9.39744C19.2544 9.14807 18.6244 9.01682 17.9812 9.01682H14.2669C13.7681 9.01682 13.2694 9.09557 12.7837 9.25307C12.6525 9.29244 12.5606 9.38432 12.495 9.50244C12.4425 9.62057 12.4294 9.75182 12.4688 9.88307C12.5344 10.0799 12.7312 10.2112 12.9412 10.2112C12.9937 10.2112 13.0462 10.2112 13.0987 10.1849C13.4794 10.0537 13.86 9.98807 14.2669 9.98807H17.9812C18.4931 9.98807 18.9919 10.0931 19.4513 10.2899C19.8975 10.4868 20.3044 10.7624 20.6456 11.1168C20.9869 11.4712 21.2494 11.8781 21.4331 12.3243C21.63 12.7968 21.7219 13.2956 21.7088 13.7943C21.6825 15.3431 21.3019 16.6556 20.5931 17.6793C20.1337 18.3356 19.5431 18.8868 18.8213 19.2806C18.0469 19.7137 17.1281 20.0024 16.0781 20.1206C15.855 20.1468 15.645 20.0943 15.4481 19.9631C15.225 19.8056 15.0806 19.5693 15.0413 19.2937C14.9888 18.7818 15.3562 18.3224 15.8681 18.2568C17.3644 18.0862 18.4013 17.5481 19.0575 16.6031C19.5563 15.8812 19.8188 14.9099 19.845 13.7418C19.845 13.4662 19.635 13.2431 19.3594 13.2431C19.0969 13.2562 18.8606 13.4531 18.8606 13.7287C18.8475 14.7131 18.6375 15.4874 18.2437 16.0518C17.7581 16.7474 16.9444 17.1543 15.75 17.2856C15.6581 17.2856 15.5794 17.3118 15.5006 17.3381C15.1463 16.4718 14.2931 15.9074 13.3481 15.9074H10.4475C9.46313 15.9074 8.59688 16.5243 8.25562 17.4299C8.1375 17.3906 8.00625 17.3643 7.88813 17.3512C6.49688 17.1937 5.53875 16.7212 4.97437 15.9074C4.515 15.2512 4.27875 14.3456 4.2525 13.2037C4.2525 12.9281 4.02938 12.7181 3.75375 12.7181C3.6225 12.7181 3.49125 12.7706 3.39937 12.8756C3.3075 12.9674 3.255 13.0987 3.26813 13.2299C3.29437 14.5687 3.59625 15.6581 4.16062 16.4849C4.89563 17.5481 6.07688 18.1649 7.77 18.3487C8.07188 18.3881 8.3475 18.5324 8.53125 18.7687C8.72812 19.0049 8.80688 19.3068 8.7675 19.6087C8.72812 19.9237 8.57063 20.2124 8.3475 20.3831L8.295 20.4224C8.07188 20.5799 7.77 20.6456 7.5075 20.6193C6.3 20.4881 5.22375 20.1599 4.33125 19.6481C3.49125 19.1756 2.80875 18.5456 2.27063 17.7843C1.44375 16.5899 1.01063 15.0674 0.97125 13.2824C0.97125 12.6918 1.06313 12.1143 1.28625 11.5631C1.49625 11.0381 1.81125 10.5656 2.21813 10.1587C2.625 9.75182 3.0975 9.42369 3.6225 9.20057C4.16062 8.96432 4.73813 8.84619 5.34188 8.84619H9.54187C10.1456 8.84619 10.7362 8.96432 11.2744 9.21369C11.5106 9.31869 11.8125 9.21369 11.9306 8.96432C12.0356 8.71494 11.9306 8.42619 11.6812 8.30807C10.9987 8.00619 10.29 7.86182 9.555 7.86182H5.355C4.62 7.86182 3.91125 8.00619 3.24187 8.29494C2.59875 8.57057 2.02125 8.96432 1.5225 9.47619C1.02375 9.97494 0.643125 10.5656 0.380625 11.1956C0.118125 11.8649 0 12.5737 0 13.3087C0.039375 15.3037 0.538125 16.9968 1.47 18.3618C1.96875 19.0837 2.59875 19.7006 3.32063 20.1993V21.9449C3.32063 22.2206 3.54375 22.4437 3.81938 22.4437C4.095 22.4437 4.31812 22.2206 4.31812 21.9449V20.7768C5.23688 21.2099 6.27375 21.4987 7.41563 21.6299C7.65188 21.6562 7.90125 21.6299 8.12438 21.5906V21.9449C8.12438 22.2206 8.3475 22.4437 8.62313 22.4437C8.89875 22.4437 9.12187 22.2206 9.12187 21.9449V21.0656C9.48938 20.7243 9.72563 20.2518 9.77812 19.7531C9.84375 19.1887 9.68625 18.6243 9.31875 18.1781C9.25313 18.0993 9.20063 18.0337 9.135 17.9812C9.26625 17.3774 9.80437 16.9312 10.4344 16.9312H13.335C13.9256 16.9312 14.4375 17.3118 14.6081 17.8631C14.1881 18.2699 13.9781 18.8606 14.0437 19.4512C14.0962 19.9237 14.3194 20.3568 14.6737 20.6718V21.9581C14.6737 22.2337 14.8969 22.4568 15.1725 22.4568C15.4481 22.4568 15.6712 22.2337 15.6712 21.9581V21.1312C15.8419 21.1574 16.0125 21.1574 16.1831 21.1312C17.1544 21.0262 18.0338 20.7768 18.8081 20.4224V21.9449C18.8081 22.2206 19.0312 22.4437 19.3069 22.4437C19.5825 22.4437 19.8056 22.2206 19.8056 21.9449V19.8581C20.4356 19.4249 20.9737 18.8868 21.4069 18.2699C22.2337 17.0756 22.6669 15.5793 22.7062 13.8337C22.7062 13.1906 22.6013 12.5606 22.365 11.9699Z" fill="#062943" />
                        <path d="M9.24023 12.9149C9.24023 14.3718 10.4346 15.5662 11.8915 15.5662C13.3484 15.5662 14.5427 14.3718 14.5427 12.9149C14.5427 11.458 13.3484 10.2637 11.8915 10.2637C10.4346 10.2637 9.24023 11.458 9.24023 12.9149ZM13.5584 12.9149C13.5584 13.8337 12.8102 14.5818 11.9046 14.5818C10.999 14.5818 10.2377 13.8337 10.2377 12.9149C10.2377 11.9962 10.9859 11.248 11.9046 11.248C12.8234 11.248 13.5584 11.9962 13.5584 12.9149Z" fill="#062943" />
                    </svg>
                    <span>
                        Family
                    </span>
                </div>
                <div class="bird-packages__tag__card">
                    <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.0007 18.3754H13.3323C10.4654 18.3754 9.30281 17.1225 8.27567 16.0051C7.40656 15.0683 6.58259 14.1766 4.68635 13.9508C3.12872 13.7702 2.29347 14.312 1.48079 14.8425C1.26634 14.9892 1.05187 15.1247 0.837418 15.2376C0.566526 15.3956 0.216632 15.294 0.0698989 15.0231C-0.0768342 14.7522 0.0134659 14.4023 0.284358 14.2556C0.47624 14.154 0.656834 14.0298 0.848716 13.9057C1.72912 13.33 2.83526 12.6077 4.79922 12.8334C7.11309 13.1043 8.1628 14.233 9.08835 15.2376C10.0816 16.3098 10.9394 17.2467 13.3097 17.2467H16.9781C17.2603 17.2805 17.5086 17.5176 17.5086 17.811C17.5086 18.1271 17.2828 18.3754 16.9781 18.3754H17.0007Z" fill="#062943" />
                        <path d="M5.30732 13.6346C5.20573 13.6346 5.10416 13.612 5.01386 13.5555C4.74297 13.3975 4.66396 13.0476 4.82198 12.7767L8.16297 7.20087C8.32099 6.92998 8.6709 6.85097 8.94179 7.00899C9.21268 7.16701 9.29169 7.5169 9.13367 7.78779L5.79268 13.3637C5.69109 13.5442 5.4992 13.6346 5.30732 13.6346Z" fill="#062943" />
                        <path d="M14.5516 11.2754C14.3146 11.2754 14.0663 11.2077 13.8518 11.0835L3.2532 4.96585C2.9033 4.76268 2.66625 4.43536 2.58724 4.04031C2.50823 3.66783 2.58725 3.30665 2.82428 3.00189C5.10429 -0.0343542 9.3144 -0.892183 12.6103 1.00406C15.9061 2.9003 17.2719 6.97497 15.7707 10.474C15.6239 10.8239 15.3418 11.0722 14.9918 11.1964C14.8564 11.2415 14.7097 11.2641 14.5629 11.2641L14.5516 11.2754ZM8.84034 1.1395C6.88767 1.1395 4.96885 2.0312 3.72726 3.69041C3.71597 3.7017 3.68211 3.74684 3.6934 3.82585C3.70469 3.89357 3.74984 3.96129 3.81756 3.99515L14.4162 10.1128C14.4839 10.1467 14.5629 10.1579 14.6194 10.1354C14.6871 10.1128 14.7097 10.0564 14.7209 10.0451C15.9851 7.07655 14.8338 3.6114 12.0346 1.99734C11.0301 1.42169 9.92391 1.1395 8.84034 1.1395Z" fill="#062943" />
                        <path d="M12.1696 8.89405C12.0793 8.89405 11.9777 8.87146 11.8874 8.81503C11.6165 8.65701 11.5262 8.3184 11.6842 8.04751C12.6775 6.32057 12.8017 4.84194 12.7226 3.89382C12.6324 2.71996 12.2147 2.09917 12.0341 1.99759C11.5939 1.74927 8.64797 1.87343 6.44697 5.5079C6.28895 5.77879 5.93906 5.8578 5.66817 5.69978C5.39728 5.54176 5.31825 5.19185 5.47627 4.92096C7.80143 1.08332 11.402 0.327073 12.5872 1.01559C13.2418 1.38807 13.7272 2.48292 13.8288 3.80352C13.9191 4.90967 13.7836 6.6366 12.6436 8.60057C12.542 8.78116 12.3502 8.88275 12.1583 8.88275L12.1696 8.89405Z" fill="#062943" />
                        <path d="M9.05474 14.7744C8.19692 14.7744 7.36165 14.6841 6.72957 14.5148C6.42482 14.4358 6.24422 14.131 6.32323 13.8263C6.40224 13.5215 6.70699 13.3522 7.01175 13.4199C7.56482 13.5666 8.28721 13.6457 9.05474 13.6457C10.5334 13.6457 11.4815 13.3522 11.7072 13.1829C11.7636 12.9346 11.9894 12.7427 12.2603 12.7427C12.5763 12.7427 12.8246 12.991 12.8246 13.307C12.8246 14.6954 9.68682 14.7744 9.05474 14.7744Z" fill="#062943" />
                        <path d="M14.9573 14.7744C13.4561 14.7744 11.6953 14.3906 11.6953 13.307C11.6953 12.991 11.9436 12.7427 12.2597 12.7427C12.5419 12.7427 12.7676 12.9459 12.8127 13.2168C12.9933 13.3748 13.7609 13.6457 14.9573 13.6457C15.5217 13.6457 16.0747 13.5779 16.5036 13.4651C16.8084 13.386 17.1131 13.5554 17.1922 13.8601C17.2712 14.1649 17.1019 14.4696 16.7971 14.5486C16.2666 14.6954 15.6345 14.7631 14.9573 14.7631V14.7744Z" fill="#062943" />
                    </svg>
                    <span>
                        BEACH
                    </span>
                </div>
                <div class="bird-packages__tag__card">
                    <svg width="23" height="18" viewBox="0 0 23 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.34061 17.7604H1.24302C0.566013 17.7604 0 17.2055 0 16.5285V8.80396C0 5.25247 2.89667 2.3669 6.45926 2.3669H8.22392C8.3904 1.63441 8.70113 0.935203 9.14507 0.313692C9.32265 0.0695275 9.61121 -0.0414612 9.89977 0.0140308L16.7364 1.34585C17.8351 1.54562 18.6231 2.51118 18.6231 3.63212V9.936C18.6231 9.936 18.6786 9.936 18.7119 9.936C18.7785 9.936 18.9339 9.9582 19.0449 9.9582V9.27011C19.0449 8.85947 19.3778 8.51541 19.7995 8.51541H21.5642C21.7862 8.51541 21.9859 8.60419 22.1302 8.77067C22.2745 8.92605 22.33 9.13692 22.3078 9.34779V10.0692C22.3078 11.7229 21.1314 13.3987 18.4899 13.3987C16.6476 13.3987 15.4934 12.4109 15.0494 11.2012L14.8497 16.5618C14.8164 17.2277 14.2836 17.7493 13.6066 17.7493H11.7976C11.165 17.7493 10.6323 17.2499 10.5768 16.6172L10.4103 14.8859C10.377 14.4531 10.0108 14.1201 9.57792 14.1201H5.53809C5.10525 14.1201 4.7501 14.4531 4.70571 14.8859L4.53924 16.6505C4.45045 17.3053 3.93993 17.7715 3.30732 17.7715L3.34061 17.7604ZM6.45926 3.47674C3.50709 3.47674 1.10984 5.86289 1.10984 8.80396V16.5285C1.10984 16.595 1.16533 16.6505 1.24302 16.6505H3.34061C3.4072 16.6505 3.46271 16.5951 3.4738 16.5174L3.64027 14.786C3.72906 13.7983 4.57255 13.0103 5.5714 13.0103H9.61122C10.6101 13.0103 11.4646 13.7872 11.5423 14.786L11.7088 16.5174C11.7088 16.584 11.7643 16.6394 11.8198 16.6394H13.6288C13.6954 16.6394 13.7509 16.584 13.762 16.5174L14.1283 6.71747L15.4712 7.76073C15.793 8.00489 15.9928 8.39333 15.9928 8.81507V10.0692C15.9928 11.1346 16.7808 12.2889 18.5232 12.2889C21.0981 12.2889 21.2312 10.4354 21.2312 10.0692V9.62525H20.1769V10.047C20.1769 10.4243 20.0104 10.6574 19.8661 10.7795C19.4888 11.1235 18.9228 11.068 18.6231 11.0348C17.9905 11.0348 17.5466 10.5797 17.5466 10.047V3.62101C17.5466 3.0328 17.1359 2.53337 16.5588 2.42238L9.95527 1.13498C9.58902 1.6899 9.35595 2.33359 9.27826 2.9773L9.21168 3.46563H6.49255L6.45926 3.47674ZM21.209 9.2812C21.209 9.2812 21.209 9.2923 21.209 9.3034V9.2812Z" fill="#062943" />
                        <path d="M11.609 8.6153C11.4425 8.6153 11.2427 8.582 11.0207 8.49321C9.922 7.99378 8.76776 7.30569 8.27943 5.92948C7.8022 4.57548 8.10187 2.86634 9.17842 0.702148L10.1662 1.20157C9.23391 3.05501 8.95644 4.51999 9.32269 5.56324C9.66674 6.5399 10.5435 7.06152 11.4425 7.47216C11.5535 7.51656 11.609 7.50546 11.609 7.50546C11.6422 7.47216 11.7532 7.27239 11.842 7.12811C11.9086 7.00603 11.9863 6.87286 12.064 6.73968C12.3858 6.31794 12.7299 6.22915 12.9741 6.21805C13.8175 6.17366 14.5389 6.99493 14.994 7.47216C15.0717 7.56095 15.1382 7.62754 15.1826 7.66083L14.4945 8.52651C14.428 8.47101 14.3281 8.37114 14.1949 8.22686C13.3847 7.35009 13.1072 7.30569 13.0407 7.31678C12.9519 7.43887 12.8853 7.54985 12.8298 7.66083C12.6189 8.03818 12.3082 8.6153 11.6201 8.6153H11.609Z" fill="#062943" />
                    </svg>
                    <span>
                        BUSH
                    </span>
                </div>
                <div class="bird-packages__tag__card">
                    <svg width="18" height="22" viewBox="0 0 18 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.8857 15.7106C12.3442 15.7106 11.8028 15.5728 11.3205 15.2873C10.5921 14.864 10.0704 14.1848 9.85379 13.3776C9.40098 11.7042 10.405 9.97168 12.0883 9.51887C13.7617 9.06606 15.5041 10.0701 15.9471 11.7436C16.3999 13.417 15.3958 15.1495 13.7126 15.6023C13.4369 15.6712 13.1613 15.7106 12.8857 15.7106ZM12.8955 10.395C12.7085 10.395 12.5214 10.4147 12.3344 10.4737C11.1827 10.7789 10.4838 11.97 10.7988 13.1217C10.9464 13.6828 11.3008 14.1454 11.8029 14.4309C12.3049 14.7164 12.8955 14.7951 13.4468 14.6475C14.5985 14.3423 15.2875 13.1512 14.9824 11.9995C14.7264 11.0348 13.8503 10.395 12.8857 10.395H12.8955Z" fill="#062943" />
                        <path d="M3.14051 18.3191C2.5991 18.3191 2.0577 18.1813 1.57536 17.8958C0.846918 17.4725 0.325209 16.7933 0.108647 15.9861C-0.107916 15.1789 0.000372693 14.3225 0.423654 13.5941C0.846935 12.8656 1.52614 12.3439 2.34317 12.1273C4.0166 11.6844 5.75896 12.6786 6.20193 14.352C6.41849 15.1691 6.3102 16.0156 5.88692 16.7441C5.46364 17.4725 4.78441 17.9942 3.96738 18.2108C3.69175 18.2797 3.41613 18.3191 3.14051 18.3191ZM3.15036 13.0133C2.96333 13.0133 2.77629 13.033 2.58926 13.092C2.02817 13.2397 1.56551 13.6039 1.2702 14.1059C0.984733 14.608 0.905982 15.1888 1.05364 15.7499C1.20129 16.3109 1.56551 16.7736 2.06754 17.0591C2.56957 17.3445 3.15037 17.4233 3.71146 17.2756C4.27256 17.128 4.73521 16.7638 5.03052 16.2716C5.31599 15.7695 5.39474 15.1887 5.24709 14.6277C4.99115 13.663 4.11505 13.0231 3.15036 13.0231V13.0133Z" fill="#062943" />
                        <path d="M8.3084 15.2379C8.10168 15.2379 7.9048 15.1099 7.84574 14.9032L6.77278 11.6646L2.87466 10.2963C2.73685 10.2471 2.62857 10.1388 2.56951 10.001C2.51044 9.86321 2.53011 9.70571 2.60886 9.57774L5.72934 4.42948C5.80809 4.30151 5.93607 4.2129 6.08373 4.19321C6.23139 4.17352 6.37902 4.22276 6.4873 4.33104L9.48966 7.24478L11.2812 6.67386C11.5372 6.59511 11.8128 6.7329 11.9014 6.98883C11.9801 7.24477 11.8423 7.5204 11.5864 7.60899L9.50934 8.26854C9.33215 8.31776 9.14512 8.26854 9.01715 8.15042L6.25106 5.45321L3.77044 9.54821L7.32402 10.7885C7.47168 10.8378 7.57996 10.9559 7.62918 11.0937L8.77106 14.5587C8.85965 14.8146 8.71199 15.0902 8.45605 15.1788C8.40683 15.1985 8.34779 15.2084 8.29857 15.2084L8.3084 15.2379Z" fill="#062943" />
                        <path d="M8.84945 3.61264C8.0521 3.61264 7.31382 3.0811 7.09726 2.27391C6.84132 1.30922 7.41225 0.315002 8.37694 0.0590644C8.83959 -0.0590606 9.33177 -5.33927e-06 9.75505 0.236245C10.1783 0.482338 10.4737 0.86624 10.6016 1.33874C10.8576 2.30343 10.2866 3.29765 9.32193 3.55358C9.16443 3.59296 9.00695 3.61264 8.84945 3.61264ZM8.84945 0.984379C8.78054 0.984379 8.70179 0.984378 8.63288 1.01391C8.18991 1.13203 7.93398 1.58485 8.0521 2.02781C8.17023 2.47078 8.63288 2.72672 9.06601 2.60859C9.50898 2.49047 9.76491 2.03766 9.64679 1.59469C9.58773 1.37812 9.4499 1.20093 9.26287 1.09265C9.1349 1.02374 8.9971 0.984379 8.84945 0.984379Z" fill="#062943" />
                        <path d="M7.06814 21.4789C6.97955 21.4789 6.89094 21.4592 6.81219 21.4002L4.08545 19.707L2.95343 20.8095C2.75655 20.9966 2.45141 20.9966 2.25453 20.8095C2.0675 20.6127 2.0675 20.3075 2.25453 20.1106L3.66218 18.7423C3.81968 18.5848 4.07563 18.5553 4.26266 18.6735L6.93031 20.3272L8.30843 18.4963C8.36749 18.4175 8.45609 18.3584 8.55452 18.3191L12.7874 16.9606C12.8661 16.9409 12.9448 16.9311 13.0137 16.941L14.8447 17.2461L15.77 15.0214C15.8291 14.8934 15.9374 14.7852 16.0752 14.7458L17.217 14.3717C17.473 14.293 17.7486 14.4308 17.8372 14.6867C17.9258 14.9427 17.7781 15.2183 17.5222 15.3069L16.5969 15.6022L15.6125 17.9745C15.5239 18.1813 15.2975 18.3092 15.0809 18.2699L12.9842 17.9155L9.01718 19.1853L7.47171 21.2525C7.37327 21.3805 7.22562 21.4494 7.07796 21.4494L7.06814 21.4789Z" fill="#062943" />
                    </svg>
                    <span>
                        ADVENTURE
                    </span>
                </div>
                <div class="bird-packages__tag__card">
                    <svg width="21" height="18" viewBox="0 0 21 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M19.6014 3.9111L15.5589 2.50673L13.6426 1.83735C12.737 1.50923 11.7395 1.87673 11.2539 2.69048C11.0045 2.63798 10.742 2.61173 10.4664 2.61173C10.4664 2.61173 10.4533 2.61173 10.4401 2.61173C10.4401 2.61173 10.427 2.61173 10.4139 2.61173C10.1514 2.61173 9.88889 2.63798 9.62639 2.69048C9.14077 1.88986 8.15639 1.50923 7.23764 1.83735L5.32139 2.50673L1.27889 3.9111C0.438893 4.23923 -0.0598573 5.0661 0.00576773 5.93235C0.0320177 6.2211 0.163268 6.4836 0.360143 6.68048L5.16389 11.3267C5.38702 11.5499 5.51827 11.8517 5.51827 12.1667V16.6686C5.51827 16.9442 5.74139 17.1542 6.00389 17.1542C6.26639 17.1542 6.48952 16.9311 6.48952 16.6686V11.458C6.48952 11.3267 6.43702 11.2086 6.34514 11.1167L1.01639 6.1161C0.819518 5.5911 1.08202 5.0136 1.60702 4.81673L3.02452 4.31798C3.02452 4.31798 3.02452 4.3311 3.02452 4.34423L7.55264 2.7561C7.94639 2.61173 8.37952 2.74298 8.65514 3.03173C7.76264 3.47798 7.06702 4.25236 6.72577 5.19736L4.38952 5.9586C4.24514 5.98485 4.12702 6.02423 4.07452 6.06361L7.25077 8.85923C7.95952 9.81735 9.10139 10.4342 10.3876 10.4342C10.3876 10.4342 10.4008 10.4342 10.4139 10.4342C10.4139 10.4342 10.427 10.4342 10.4401 10.4342C11.7264 10.4342 12.8551 9.81735 13.577 8.85923L16.7533 6.06361C16.7533 6.06361 16.5826 5.99798 16.4383 5.9586L14.102 5.19736C13.7608 4.25236 13.0651 3.47798 12.1726 3.03173C12.4483 2.72985 12.8683 2.61173 13.2751 2.7561L17.8033 4.34423C17.8033 4.34423 17.8033 4.3311 17.8033 4.31798L19.2208 4.81673C19.7458 5.0136 19.9951 5.60423 19.8114 6.1161L14.4826 11.1167C14.3908 11.2086 14.3383 11.3267 14.3383 11.458V16.6686C14.3383 16.9442 14.5614 17.1542 14.8239 17.1542C15.0864 17.1542 15.3095 16.9311 15.3095 16.6686V12.1667C15.3095 11.8517 15.4408 11.5499 15.6639 11.3267L20.4676 6.68048C20.6776 6.4836 20.8089 6.2211 20.822 5.93235C20.8876 5.07923 20.402 4.23923 19.5489 3.9111H19.6014ZM6.18764 6.5361L6.52889 6.4311C6.52889 6.4311 6.52889 6.49673 6.52889 6.52298C6.52889 6.62798 6.52889 6.73298 6.52889 6.83798L6.18764 6.52298V6.5361ZM14.6926 6.5361L14.3514 6.8511C14.3514 6.7461 14.3514 6.6411 14.3514 6.5361C14.3514 6.49673 14.3514 6.47048 14.3514 6.44423L14.6926 6.54923V6.5361ZM13.3933 6.52298C13.3933 8.12423 12.0939 9.43673 10.4926 9.44985C10.4926 9.44985 10.4533 9.44985 10.4401 9.44985C10.427 9.44985 10.4008 9.44985 10.3876 9.44985C8.78639 9.44985 7.48702 8.13735 7.48702 6.52298C7.48702 4.9086 8.78639 3.60923 10.3876 3.5961C10.3876 3.5961 10.427 3.5961 10.4401 3.5961C10.4533 3.5961 10.4795 3.5961 10.4926 3.5961C12.0939 3.5961 13.3933 4.9086 13.3933 6.52298Z" fill="#062943" />
                        <path d="M19.4962 3.9375H18.4724V2.53313C18.4724 1.70625 17.803 1.03688 16.9762 1.03688H4.20555C3.37867 1.03688 2.7093 1.70625 2.7093 2.53313V4.12125H1.68555V2.53313C1.68555 1.14188 2.8143 0 4.20555 0H16.9762C18.3674 0 19.5093 1.12875 19.5093 2.53313V3.9375H19.4962Z" fill="#062943" />
                        <path d="M5.57098 10.697H4.19285C3.36598 10.697 2.6966 10.0276 2.6966 9.20076V7.79639H1.67285V9.20076C1.67285 10.592 2.8016 11.7339 4.20598 11.7339H5.96473C5.72848 11.4451 5.61035 11.0776 5.5841 10.7101L5.57098 10.697Z" fill="#062943" />
                        <path d="M18.4595 7.59955V9.18767C18.4595 10.0145 17.7901 10.6839 16.9632 10.6839H15.6507C15.6507 10.6839 15.6507 10.6839 15.6507 10.6971C15.6507 10.9333 15.6113 11.1433 15.5063 11.3533C15.467 11.4846 15.362 11.5896 15.2832 11.7077H16.9632C18.3545 11.7077 19.4832 10.5789 19.4832 9.17455V7.58643H18.4595V7.59955Z" fill="#062943" />
                    </svg>
                    <span>
                        RELAX
                    </span>
                </div>
                <div class="bird-packages__tag__card">
                    <svg width="22" height="17" viewBox="0 0 22 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_5442_9625)">
                            <path d="M20.7244 0.800508L18.375 0.419883C18.0994 0.380508 17.85 0.564258 17.8106 0.826758C17.7713 1.10238 17.955 1.35176 18.2175 1.39113L19.7138 1.62738L18.0863 2.66426C17.1806 1.02363 15.4613 -0.0263672 13.5713 -0.0263672C12.1931 -0.0263672 10.8938 0.511758 9.9225 1.49613L9.46313 1.95551L9.00375 1.49613C8.05875 0.524883 6.73313 -0.0263672 5.34188 -0.0263672C3.95063 -0.0263672 2.625 0.551133 1.69313 1.52238C0.721877 2.48051 0.170628 3.80613 0.170628 5.17113C0.170628 6.53613 0.721877 7.86176 1.69313 8.81988L4.39688 11.5236L2.8875 12.4949L0.577503 12.1274C0.301878 12.088 0.0525026 12.2718 0.0131276 12.5343C-0.0262474 12.8099 0.157503 13.0593 0.420003 13.0986L2.48063 13.4268L2.12625 15.5924C2.08688 15.868 2.27063 16.1174 2.53313 16.1568C2.55938 16.1568 2.58563 16.1568 2.61188 16.1568C2.84813 16.1568 3.05813 15.9861 3.0975 15.7368L3.49125 13.2693L5.10563 12.2324L8.58375 15.7105C8.82 15.9468 9.135 16.0649 9.45 16.0649C9.765 16.0649 10.08 15.9468 10.3163 15.7105L17.0363 8.99051C18.4931 7.53363 19.0313 5.43363 18.4669 3.60926L20.1338 2.54613L19.9106 3.89801C19.8713 4.17363 20.055 4.42301 20.3175 4.46238C20.3438 4.46238 20.37 4.46238 20.3963 4.46238C20.6325 4.46238 20.8425 4.29176 20.8819 4.04238L21.2756 1.57488C21.3281 1.19426 21.0788 0.839883 20.6981 0.787383L20.7244 0.800508ZM16.3406 8.28176L9.62063 15.0018C9.52875 15.0936 9.38438 15.0936 9.2925 15.0018L5.97188 11.6811L9.68625 9.27926L10.8544 10.4999C10.9463 10.6049 11.0775 10.6574 11.2088 10.6574C11.34 10.6574 11.4581 10.6049 11.55 10.513C11.7469 10.3293 11.76 10.0143 11.5631 9.80426L8.2425 6.33926C8.05875 6.14238 7.74375 6.12926 7.53375 6.32613C7.33688 6.50988 7.32375 6.82488 7.52063 7.03488L8.96438 8.54426L5.23688 10.9593L2.37563 8.09801C1.60125 7.32363 1.155 6.24738 1.155 5.14488C1.155 4.04238 1.60125 2.96613 2.38875 2.19176C3.15 1.41738 4.21313 0.971133 5.32875 0.971133C6.44438 0.971133 7.52063 1.41738 8.28188 2.19176L9.43688 3.34676L10.5919 2.19176C11.3794 1.40426 12.4294 0.971133 13.545 0.971133C15.1856 0.971133 16.6819 1.94238 17.3644 3.45176C18.06 5.01363 17.64 6.94301 16.3275 8.25551L16.3406 8.28176Z" fill="#062943" />
                        </g>
                        <defs>
                            <clipPath id="clip0_5442_9625">
                                <rect width="21.315" height="16.1831" fill="white" />
                            </clipPath>
                        </defs>
                    </svg>
                    <span>
                        ROMANTIC
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="bird-packages__two">
        <div class="bird-packages__two__content">
            <div class="bird-packages-grid__card">
                <div class="bird-packages-grid__card__info">
                    <span class="bird-packages__content__label">PACKAGED EXPERIENCES</span>
                    <div class="bird-packages__content__title__tag">
                        <h3 class="bird-packages__content__title">Family Moments</h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.8359 15.9141L21.9141 14.8359L12.5391 5.46094L12 4.94531L11.4609 5.46094L2.08594 14.8359L3.16406 15.9141L12 7.07813L20.8359 15.9141Z" fill="black" />
                        </svg>
                    </div>
                    <div class="bird-packages__content__description-wrapper">
                        <p class="bird-packages__content__description">If a picture-perfect family holiday is what you're after, you're in the right place. Whether you're a single parent or a larger crew with grandma, grandpa, and a few kiddies in tow, our properties provide a second home for loved ones to come together. Being mostly moms and dads ourselves, we're committed to providing every comfort and convenience with a good dose of fun! Kids really are king, and aside from fishing trips, boat rides, and safari drives, we've got pools to splash in and plenty of space to tire out those little legs. Our Dream Xplorers programme provides some healthy distraction, much to the delight of the grown-ups!</p>
                    </div>
                    <a href="#" class="bys-package-button button--theme-2">View Package</a>
                </div>
                <div class="bird-packages-grid__card__image" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/family.png'); ?>')"></div>
            </div>
            <div class="bird-packages-grid__card">
                <div class="bird-packages-grid__card__image" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/beach.png'); ?>')"></div>
                <div class="bird-packages-grid__card__info">
                    <span class="bird-packages__content__label">Sand & Waves</span>
                    <div class="bird-packages__content__title__tag">
                        <h3 class="bird-packages__content__title">Beach Adventures</h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.8359 15.9141L21.9141 14.8359L12.5391 5.46094L12 4.94531L11.4609 5.46094L2.08594 14.8359L3.16406 15.9141L12 7.07813L20.8359 15.9141Z" fill="black" />
                        </svg>
                    </div>
                    <div class="bird-packages__content__description-wrapper">
                        <p class="bird-packages__content__description">It's no secret that we at Dream Hotels & Resorts love the beach. We also appreciate everything that comes with it: wiggling our toes in the sand, making a splash near the shoreline, but most of all, sprawling out on the sunlounger, cocktail in hand. Whether you want to squeeze in as much action as possible or catch up on some R&R, we've got some of the best beaches right on the doorstep of our most beloved coastal properties. This is your chance to swim, surf, SUP, scuba, and snorkel to your heart's content. Because at the end of the day, life is a beach, right?</p>
                    </div>
                    <a href="#" class="bys-package-button button--theme-2">View Package</a>
                </div>
            </div>
            <div class="bird-packages-grid__card">
                <div class="bird-packages-grid__card__info">
                    <span class="bird-packages__content__label">African Wilderness</span>
                    <div class="bird-packages__content__title__tag">
                        <h3 class="bird-packages__content__title">Bush Experiences</h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.8359 15.9141L21.9141 14.8359L12.5391 5.46094L12 4.94531L11.4609 5.46094L2.08594 14.8359L3.16406 15.9141L12 7.07813L20.8359 15.9141Z" fill="black" />
                        </svg>
                    </div>
                    <div class="bird-packages__content__description-wrapper">
                        <p class="bird-packages__content__description">
                            If there's one thing we do right and well at Dream Hotels & Resorts, it's that quintessential South African safari. Just picture it: expert-led game drives with sundowners on cue, stargazing around the fire, boma feasts, and birding galore. Nature-lovers, this one's for you! Upon your return to the wilderness, we'll ensure your experience is tailored to you. Looking to tick the Big 5 off your wildlife list? Our experienced tracker guides know the way to go. For a fresh perspective, set out on a sunset barge cruise, try a segway tour or move with the pace of nature on a guided bush walk. Now, this is the stuff safari dreams are made of…
                        </p>
                    </div>
                    <a href="#" class="bys-package-button button--theme-2">View Package</a>
                </div>
                <div class="bird-packages-grid__card__image" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/bush.png'); ?>')"></div>
            </div>
            <div class="bird-packages-grid__card">
                <div class="bird-packages-grid__card__image" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/exhilarating.png'); ?>')"></div>
                <div class="bird-packages-grid__card__info">
                    <span class="bird-packages__content__label">Exploration</span>
                    <div class="bird-packages__content__title__tag">
                        <h3 class="bird-packages__content__title">Exhilarating Adventures</h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.8359 15.9141L21.9141 14.8359L12.5391 5.46094L12 4.94531L11.4609 5.46094L2.08594 14.8359L3.16406 15.9141L12 7.07813L20.8359 15.9141Z" fill="black" />
                        </svg>
                    </div>
                    <div class="bird-packages__content__description-wrapper">
                        <p class="bird-packages__content__description">Itching to get back in the water or break in those hiking boots? Our holiday properties are located in regions brimming with outdoor activities and cultural experiences guaranteed to fuel your wanderlust. Book a stay and you'll get instant access to a treasure trove of value-adds that won't burn a hole in your budget. From the Cape Town Minstrel Carnival to Knysna's Oyster Festival. The annual Sardine Run, and whale season – time your visit with us alongside a local festival to wring every bit of richness out of your next holiday. Tell us what you're after, and we'll point you in the right direction.</p>
                    </div>
                    <a href="#" class="bys-package-button button--theme-2">View Package</a>
                </div>
            </div>
            <div class="bird-packages-grid__card">
                <div class="bird-packages-grid__card__info">
                    <span class="bird-packages__content__label">Rejuvenate the soul</span>
                    <div class="bird-packages__content__title__tag">
                        <h3 class="bird-packages__content__title">Relax & Revitalise</h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.8359 15.9141L21.9141 14.8359L12.5391 5.46094L12 4.94531L11.4609 5.46094L2.08594 14.8359L3.16406 15.9141L12 7.07813L20.8359 15.9141Z" fill="black" />
                        </svg>
                    </div>
                    <div class="bird-packages__content__description-wrapper">
                        <p class="bird-packages__content__description">Looking to escape the daily grind or simply gain a fresh perspective? We've no shortage of flexible holiday hideouts geared towards strengthening your connection with nature, and yourself. From the moment you arrive, expect service excellence, warmth and care (our 'one guest' approach!).
                            Practice your asanas, engage in meditation, walk the beach barefoot, and trace the serpentine path of our Stonehill labyrinth. Level up on relaxation something special from our pamper packages – me-time spa sessions and treatments. Or, how about a freshly prepared culinary treat with ingredients from the garden?</p>
                    </div>
                    <a href="#" class="bys-package-button button--theme-2">View Package</a>
                </div>
                <div class="bird-packages-grid__card__image" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/relax.png'); ?>')"></div>
            </div>
            <div class="bird-packages-grid__card">
                <div class="bird-packages-grid__card__image" style="background-image: url('<?php echo esc_url($plugin_url . 'assets/images/romantic.png'); ?>')"></div>
                <div class="bird-packages-grid__card__info">
                    <span class="bird-packages__content__label">Cloud nine</span>
                    <div class="bird-packages__content__title__tag">
                        <h3 class="bird-packages__content__title">Romantic Moments</h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.8359 15.9141L21.9141 14.8359L12.5391 5.46094L12 4.94531L11.4609 5.46094L2.08594 14.8359L3.16406 15.9141L12 7.07813L20.8359 15.9141Z" fill="black" />
                        </svg>
                    </div>
                    <div class="bird-packages__content__description-wrapper">
                        <p class="bird-packages__content__description">Celebrating an engagement or anniversary? Honeymoon or intimate birthday? We're here to make your special occasion completely unforgettable. If there's one thing we love, it's sweeping loved up duos off their feet with romance-inducing spoils such as candlelit dinners, sunset cruises, and his and hers private spa experiences. But as much as it's about the big stuff, we're also thoughtful about the finer details, be it strawberries and bubbly when you least expect it, special turndowns, or late morning sleep-ins together. Whether you want to go all out or keep it subtle and intimate, our teams are always willing to go the extra mile.</p>
                    </div>
                    <a href="#" class="bys-package-button button--theme-2">View Package</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const accordionTriggers = document.querySelectorAll('.bird-packages__content__title__tag');

            function isMobileView() {
                return window.innerWidth < 1024;
            }

            function closeAllAccordions() {
                accordionTriggers.forEach(trigger => {
                    const card = trigger.closest('.bird-packages-grid__card__info');
                    const wrapper = card.querySelector('.bird-packages__content__description-wrapper');

                    wrapper.style.height = '0px';
                    trigger.classList.remove('active');
                });
            }

            function toggleAccordion(trigger, wrapper, description) {
                if (!isMobileView()) return;

                const isActive = trigger.classList.contains('active');

                if (isActive) {
                    wrapper.style.height = '0px';
                    trigger.classList.remove('active');
                } else {
                    closeAllAccordions();

                    const fullHeight = description.scrollHeight;
                    wrapper.style.height = fullHeight + 'px';
                    trigger.classList.add('active');
                }
            }

            function handleResize() {
                accordionTriggers.forEach(trigger => {
                    const card = trigger.closest('.bird-packages-grid__card__info');
                    const wrapper = card.querySelector('.bird-packages__content__description-wrapper');
                    const description = card.querySelector('.bird-packages__content__description');

                    if (isMobileView()) {
                        if (!trigger.classList.contains('active')) {
                            wrapper.style.height = '0px';
                        } else {
                            const fullHeight = description.scrollHeight;
                            wrapper.style.height = fullHeight + 'px';
                        }
                    } else {
                        wrapper.style.height = 'auto';
                        trigger.classList.remove('active');
                    }
                });
            }

            accordionTriggers.forEach(trigger => {
                const card = trigger.closest('.bird-packages-grid__card__info');
                const wrapper = card.querySelector('.bird-packages__content__description-wrapper');
                const description = card.querySelector('.bird-packages__content__description');

                trigger.addEventListener('click', function() {
                    toggleAccordion(trigger, wrapper, description);
                });
            });

            window.addEventListener('resize', handleResize);

            handleResize();
        });
    </script>
</div>
