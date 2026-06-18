import ResponsiveBox from "@/components/core/ResponsiveBox";
import FooterMenu from "./components/Menu";
import FooterLogoAndSocialLinks from "./components/LogoAndSocialLinks";
import FooterCopyright from "./components/Copyright";

const Footer = () => {
  return (
    <ResponsiveBox classNames="relative overflow-hidden bg-[#050D1B] border-t border-[#FF6A00]/20">
      <div className="absolute right-0 top-0 h-80 w-80 rounded-full bg-[#FF6A00]/10 blur-[80px]" />

      <div className="relative z-10 w-full px-10 py-12">
        <div className="flex w-full items-start justify-between gap-12">
          <div className="w-[40%]">
            <FooterLogoAndSocialLinks />
          </div>

          <div className="ml-auto flex w-[60%] justify-end">
            <FooterMenu />
          </div>
        </div>

        <FooterCopyright />
      </div>
    </ResponsiveBox>
  );
};

export default Footer;