import { Suspense } from "react";

import PageBox from "@/components/core/PageBox";
import GlowDivider from "@/components/core/GlowDivider";

import HomeSection1 from "@/components/home/Section1";
import HomeSection2 from "@/components/home/Section2";
import HomeSection3 from "@/components/home/Section3";
import HomeSection4 from "@/components/home/Section4";
import HomeSection5 from "@/components/home/Section5";
import HomeSection6 from "@/components/home/Section6";

const Home = () => {
  return (
    <PageBox>
      <Suspense>
        <HomeSection1 id="home" />

        <HomeSection2 id="services" />
        <GlowDivider />

        <HomeSection3 id="projects" />
        <GlowDivider />

        {/* <HomeSection4 id="about" />
        <GlowDivider /> */}

        <HomeSection5 id="contact" />
        <GlowDivider />

        <HomeSection6 id="contact-form" />
      </Suspense>
    </PageBox>
  );
};

export default Home;