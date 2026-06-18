import Link from "next/link";
import Column from "@/components/core/Column";
import footerMenu from "@/data/footerMenu";

const FooterMenu = () => {
  return (
    <Column classNames="gap-5 items-end text-right">
      <p className="text-sm font-bold uppercase tracking-[0.3em] text-[#FF6A00]">
        Quick Links
      </p>

      <div className="grid grid-cols-2 gap-x-20 gap-y-3 text-right">
        {footerMenu.flat().map((item, index) => (
          <Link
            key={`menu-item-${index}`}
            href={item.path}
            className="footer-menu-link inline-block text-base text-[#DCE8F8]"
          >
            {item.title}
          </Link>
        ))}
      </div>
    </Column>
  );
};

export default FooterMenu;