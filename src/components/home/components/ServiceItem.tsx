const ServiceItem = ({ data }: any) => {
  const Icon = data.icon;

  return (
    <div className="flex h-full flex-col p-8">
      {/* Icon */}

      <div
        className="
          mb-6
          flex
          h-16
          w-16
          items-center
          justify-center
          rounded-2xl
          border
          border-[#FF6A00]/20
          bg-[#FF6A00]/10
          transition-all
          duration-300
          group-hover:bg-[#FF6A00]/20
          group-hover:shadow-[0_0_30px_rgba(255,106,0,0.25)]
        "
      >
        <Icon
          size={30}
          className="
            text-[#FF6A00]
            transition-all
            duration-300
            group-hover:scale-110
          "
        />
      </div>

      {/* Title */}

      <h3
        className="
          text-2xl
          font-bold
          text-white
        "
      >
        {data.title}
      </h3>

      {/* Description */}

      <p
        className="
          mt-4
          text-base
          leading-8
          text-[#DCE8F8]
        "
      >
        {data.description}
      </p>
    </div>
  );
};

export default ServiceItem;