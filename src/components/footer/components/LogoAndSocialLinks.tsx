import Image from "next/image";
import Column from "@/components/core/Column";
import Row from "@/components/core/Row";

const FooterLogoAndSocialLinks = () => {
  return (
    <Column classNames="gap-5">
      <Image
        src="/logo-horizontal-new.png"
        alt="Semraa Technology"
        width={700}
        height={180}
        priority
        className="h-16 w-fit object-contain"
      />

      <p className="max-w-xl text-base leading-7 text-[#DCE8F8]">
        Crafting technology-driven solutions that help businesses innovate,
        grow and stay ahead in a digital world.
      </p>

      <Row classNames="gap-3">
        <span className="hidden">Social links disabled</span>
      </Row>
    </Column>
  );
};

export default FooterLogoAndSocialLinks;