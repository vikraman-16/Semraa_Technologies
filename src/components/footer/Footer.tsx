import ResponsiveBox from "@/components/core/ResponsiveBox";
import FooterLogoAndSocialLinks from "./components/LogoAndSocialLinks";
import FooterCopyright from "./components/Copyright";
import ContactInfo from "./components/ContactInfo";

const Footer = () => {
  return (
    <ResponsiveBox classNames="relative overflow-hidden bg-[#050D1B] border-t border-[#FF6A00]/20">
      <div className="absolute right-0 top-0 h-80 w-80 rounded-full bg-[#FF6A00]/10 blur-[80px]" />

      <div className="relative z-10 w-full px-5 py-12 md:px-10">
        <div className="grid gap-6 md:grid-cols-[1.1fr_0.45fr]">
          <FooterLogoAndSocialLinks />

          <div className="md:flex md:justify-start">
            <ContactInfo />
          </div>
        </div>

        <FooterCopyright />
      </div>
    </ResponsiveBox>
  );
};

export default Footer;