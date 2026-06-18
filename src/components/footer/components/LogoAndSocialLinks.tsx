import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import Link from "next/link";
import Image from "next/image";
import Column from "@/components/core/Column";
import Row from "@/components/core/Row";
import socialLinks from "@/data/socialLinks";

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
        {socialLinks.slice(0, 5).map((link, index) => (
          <Link
            key={`social-link-${index}`}
            href={link.url}
            target="_blank"
            aria-label={`${link.name}`}
            className="footer-social-icon flex h-11 w-11 items-center justify-center rounded-full border border-[#FF6A00]/35 bg-[#081120] text-white"
          >
            <FontAwesomeIcon icon={link.icon} />
          </Link>
        ))}
      </Row>
    </Column>
  );
};

export default FooterLogoAndSocialLinks;