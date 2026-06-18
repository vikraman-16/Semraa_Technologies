import Image from "next/image";

const AnimatedElement = () => {
  return (
    <div className="relative w-full h-1/2 lg:h-full items-center justify-center lg:justify-end hidden md:flex">
      <Image
        src="/200.gif"
        alt="circle"
        width={1080}
        height={1080}
        sizes="100%"
        priority
        placeholder="blur"
        blurDataURL="/placeholder.png"
        className="h-full w-auto aspect-square drop_out"
      />


    </div>
  );
};

export default AnimatedElement;
