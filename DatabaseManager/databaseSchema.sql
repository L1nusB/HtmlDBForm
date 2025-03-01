USE [master]
GO
/****** Object:  Database [Serviscope]    Script Date: 30.01.2025 13:58:15 ******/
CREATE DATABASE [Serviscope]
 CONTAINMENT = NONE
 ON  PRIMARY 
( NAME = N'Serviscope', FILENAME = N'C:\Program Files\Microsoft SQL Server\MSSQL16.MSSQLSERVER\MSSQL\DATA\Serviscope.mdf' , SIZE = 8192KB , MAXSIZE = UNLIMITED, FILEGROWTH = 65536KB )
 LOG ON 
( NAME = N'Serviscope_log', FILENAME = N'C:\Program Files\Microsoft SQL Server\MSSQL16.MSSQLSERVER\MSSQL\DATA\Serviscope_log.ldf' , SIZE = 8192KB , MAXSIZE = 2048GB , FILEGROWTH = 65536KB )
 WITH CATALOG_COLLATION = DATABASE_DEFAULT, LEDGER = OFF
GO
ALTER DATABASE [Serviscope] SET COMPATIBILITY_LEVEL = 160
GO
IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
begin
EXEC [Serviscope].[dbo].[sp_fulltext_database] @action = 'enable'
end
GO
ALTER DATABASE [Serviscope] SET ANSI_NULL_DEFAULT OFF 
GO
ALTER DATABASE [Serviscope] SET ANSI_NULLS OFF 
GO
ALTER DATABASE [Serviscope] SET ANSI_PADDING OFF 
GO
ALTER DATABASE [Serviscope] SET ANSI_WARNINGS OFF 
GO
ALTER DATABASE [Serviscope] SET ARITHABORT OFF 
GO
ALTER DATABASE [Serviscope] SET AUTO_CLOSE OFF 
GO
ALTER DATABASE [Serviscope] SET AUTO_SHRINK OFF 
GO
ALTER DATABASE [Serviscope] SET AUTO_UPDATE_STATISTICS ON 
GO
ALTER DATABASE [Serviscope] SET CURSOR_CLOSE_ON_COMMIT OFF 
GO
ALTER DATABASE [Serviscope] SET CURSOR_DEFAULT  GLOBAL 
GO
ALTER DATABASE [Serviscope] SET CONCAT_NULL_YIELDS_NULL OFF 
GO
ALTER DATABASE [Serviscope] SET NUMERIC_ROUNDABORT OFF 
GO
ALTER DATABASE [Serviscope] SET QUOTED_IDENTIFIER OFF 
GO
ALTER DATABASE [Serviscope] SET RECURSIVE_TRIGGERS OFF 
GO
ALTER DATABASE [Serviscope] SET  DISABLE_BROKER 
GO
ALTER DATABASE [Serviscope] SET AUTO_UPDATE_STATISTICS_ASYNC OFF 
GO
ALTER DATABASE [Serviscope] SET DATE_CORRELATION_OPTIMIZATION OFF 
GO
ALTER DATABASE [Serviscope] SET TRUSTWORTHY OFF 
GO
ALTER DATABASE [Serviscope] SET ALLOW_SNAPSHOT_ISOLATION OFF 
GO
ALTER DATABASE [Serviscope] SET PARAMETERIZATION SIMPLE 
GO
ALTER DATABASE [Serviscope] SET READ_COMMITTED_SNAPSHOT OFF 
GO
ALTER DATABASE [Serviscope] SET HONOR_BROKER_PRIORITY OFF 
GO
ALTER DATABASE [Serviscope] SET RECOVERY FULL 
GO
ALTER DATABASE [Serviscope] SET  MULTI_USER 
GO
ALTER DATABASE [Serviscope] SET PAGE_VERIFY CHECKSUM  
GO
ALTER DATABASE [Serviscope] SET DB_CHAINING OFF 
GO
ALTER DATABASE [Serviscope] SET FILESTREAM( NON_TRANSACTED_ACCESS = OFF ) 
GO
ALTER DATABASE [Serviscope] SET TARGET_RECOVERY_TIME = 60 SECONDS 
GO
ALTER DATABASE [Serviscope] SET DELAYED_DURABILITY = DISABLED 
GO
ALTER DATABASE [Serviscope] SET ACCELERATED_DATABASE_RECOVERY = OFF  
GO
EXEC sys.sp_db_vardecimal_storage_format N'Serviscope', N'ON'
GO
ALTER DATABASE [Serviscope] SET QUERY_STORE = ON
GO
ALTER DATABASE [Serviscope] SET QUERY_STORE (OPERATION_MODE = READ_WRITE, CLEANUP_POLICY = (STALE_QUERY_THRESHOLD_DAYS = 30), DATA_FLUSH_INTERVAL_SECONDS = 900, INTERVAL_LENGTH_MINUTES = 60, MAX_STORAGE_SIZE_MB = 1000, QUERY_CAPTURE_MODE = AUTO, SIZE_BASED_CLEANUP_MODE = AUTO, MAX_PLANS_PER_QUERY = 200, WAIT_STATS_CAPTURE_MODE = ON)
GO
USE [Serviscope]
GO
/****** Object:  Table [dbo].[USEAP_RPA_Standort]    Script Date: 30.01.2025 13:58:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[USEAP_RPA_Standort](
	[pk_RPA_Standort] [int] IDENTITY(1,1) NOT NULL,
	[Standort] [nvarchar](50) NOT NULL,
	[Standort_Kuerzel] [nvarchar](50) NOT NULL,
 CONSTRAINT [pk_Standort] PRIMARY KEY CLUSTERED 
(
	[pk_RPA_Standort] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[USEAP_RPA_Bankenuebersicht]    Script Date: 30.01.2025 13:58:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[USEAP_RPA_Bankenuebersicht](
	[pk_RPA_Bankenuebersicht] [int] IDENTITY(1,1) NOT NULL,
	[RZBK] [int] NOT NULL,
	[Name] [nvarchar](100) NOT NULL,
 CONSTRAINT [pk_Bankenuebersicht] PRIMARY KEY CLUSTERED 
(
	[pk_RPA_Bankenuebersicht] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[USEAP_RPA_Prozesse]    Script Date: 30.01.2025 13:58:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[USEAP_RPA_Prozesse](
	[pk_RPA_Prozesse] [int] IDENTITY(1,1) NOT NULL,
	[Prozessname] [nvarchar](250) NOT NULL,
 CONSTRAINT [pk_Prozess_ID] PRIMARY KEY CLUSTERED 
(
	[pk_RPA_Prozesse] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[USEAP_RPA_Prozess_Zuweisung]    Script Date: 30.01.2025 13:58:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[USEAP_RPA_Prozess_Zuweisung](
	[pk_Prozess_Zuweisung] [int] IDENTITY(1,1) NOT NULL,
	[fk_RPA_Bankenuebersicht] [int] NOT NULL,
	[fk_RPA_Prozesse] [int] NOT NULL,
	[fk_RPA_Standort] [int] NOT NULL,
	[ProduktionsStart] [date] NOT NULL,
 CONSTRAINT [pk_Prozess_Zuweisung] PRIMARY KEY CLUSTERED 
(
	[pk_Prozess_Zuweisung] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
 CONSTRAINT [UQ_EA_Prozess_Zuweisung] UNIQUE NONCLUSTERED 
(
	[fk_RPA_Bankenuebersicht] ASC,
	[fk_RPA_Prozesse] ASC,
	[fk_RPA_Standort] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  View [dbo].[USEAP_RPA_ViewProzessUebersicht]    Script Date: 30.01.2025 13:58:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW [dbo].[USEAP_RPA_ViewProzessUebersicht]
AS
SELECT        dbo.USEAP_RPA_Bankenuebersicht.RZBK, dbo.USEAP_RPA_Bankenuebersicht.Name, dbo.USEAP_RPA_Prozess_Zuweisung.ProduktionsStart, dbo.USEAP_RPA_Prozesse.Prozessname, dbo.USEAP_RPA_Standort.Standort, 
                         dbo.USEAP_RPA_Standort.Standort_Kuerzel, dbo.USEAP_RPA_Prozess_Zuweisung.pk_Prozess_Zuweisung, dbo.USEAP_RPA_Prozess_Zuweisung.fk_RPA_Bankenuebersicht, 
                         dbo.USEAP_RPA_Prozess_Zuweisung.fk_RPA_Prozesse, dbo.USEAP_RPA_Prozess_Zuweisung.fk_RPA_Standort
FROM            dbo.USEAP_RPA_Bankenuebersicht INNER JOIN
                         dbo.USEAP_RPA_Prozess_Zuweisung ON dbo.USEAP_RPA_Bankenuebersicht.pk_RPA_Bankenuebersicht = dbo.USEAP_RPA_Prozess_Zuweisung.fk_RPA_Bankenuebersicht INNER JOIN
                         dbo.USEAP_RPA_Standort ON dbo.USEAP_RPA_Prozess_Zuweisung.fk_RPA_Standort = dbo.USEAP_RPA_Standort.pk_RPA_Standort INNER JOIN
                         dbo.USEAP_RPA_Prozesse ON dbo.USEAP_RPA_Prozess_Zuweisung.fk_RPA_Prozesse = dbo.USEAP_RPA_Prozesse.pk_RPA_Prozesse
GO
ALTER TABLE [dbo].[USEAP_RPA_Prozess_Zuweisung]  WITH CHECK ADD  CONSTRAINT [fk_Bankenuebersicht] FOREIGN KEY([fk_RPA_Bankenuebersicht])
REFERENCES [dbo].[USEAP_RPA_Bankenuebersicht] ([pk_RPA_Bankenuebersicht])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[USEAP_RPA_Prozess_Zuweisung] CHECK CONSTRAINT [fk_Bankenuebersicht]
GO
ALTER TABLE [dbo].[USEAP_RPA_Prozess_Zuweisung]  WITH CHECK ADD  CONSTRAINT [fk_RPA_Prozesse] FOREIGN KEY([fk_RPA_Prozesse])
REFERENCES [dbo].[USEAP_RPA_Prozesse] ([pk_RPA_Prozesse])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[USEAP_RPA_Prozess_Zuweisung] CHECK CONSTRAINT [fk_RPA_Prozesse]
GO
ALTER TABLE [dbo].[USEAP_RPA_Prozess_Zuweisung]  WITH CHECK ADD  CONSTRAINT [fk_RPA_Standort] FOREIGN KEY([fk_RPA_Standort])
REFERENCES [dbo].[USEAP_RPA_Standort] ([pk_RPA_Standort])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[USEAP_RPA_Prozess_Zuweisung] CHECK CONSTRAINT [fk_RPA_Standort]
GO
EXEC sys.sp_addextendedproperty @name=N'MS_DiagramPane1', @value=N'[0E232FF0-B466-11cf-A24F-00AA00A3EFFF, 1.00]
Begin DesignProperties = 
   Begin PaneConfigurations = 
      Begin PaneConfiguration = 0
         NumPanes = 4
         Configuration = "(H (1[40] 4[20] 2[20] 3) )"
      End
      Begin PaneConfiguration = 1
         NumPanes = 3
         Configuration = "(H (1 [50] 4 [25] 3))"
      End
      Begin PaneConfiguration = 2
         NumPanes = 3
         Configuration = "(H (1 [50] 2 [25] 3))"
      End
      Begin PaneConfiguration = 3
         NumPanes = 3
         Configuration = "(H (4 [30] 2 [40] 3))"
      End
      Begin PaneConfiguration = 4
         NumPanes = 2
         Configuration = "(H (1 [56] 3))"
      End
      Begin PaneConfiguration = 5
         NumPanes = 2
         Configuration = "(H (2 [66] 3))"
      End
      Begin PaneConfiguration = 6
         NumPanes = 2
         Configuration = "(H (4 [50] 3))"
      End
      Begin PaneConfiguration = 7
         NumPanes = 1
         Configuration = "(V (3))"
      End
      Begin PaneConfiguration = 8
         NumPanes = 3
         Configuration = "(H (1[56] 4[18] 2) )"
      End
      Begin PaneConfiguration = 9
         NumPanes = 2
         Configuration = "(H (1 [75] 4))"
      End
      Begin PaneConfiguration = 10
         NumPanes = 2
         Configuration = "(H (1[66] 2) )"
      End
      Begin PaneConfiguration = 11
         NumPanes = 2
         Configuration = "(H (4 [60] 2))"
      End
      Begin PaneConfiguration = 12
         NumPanes = 1
         Configuration = "(H (1) )"
      End
      Begin PaneConfiguration = 13
         NumPanes = 1
         Configuration = "(V (4))"
      End
      Begin PaneConfiguration = 14
         NumPanes = 1
         Configuration = "(V (2))"
      End
      ActivePaneConfig = 0
   End
   Begin DiagramPane = 
      Begin Origin = 
         Top = 0
         Left = 0
      End
      Begin Tables = 
         Begin Table = "USEAP_RPA_Bankenuebersicht"
            Begin Extent = 
               Top = 6
               Left = 38
               Bottom = 119
               Right = 266
            End
            DisplayFlags = 280
            TopColumn = 0
         End
         Begin Table = "USEAP_RPA_Prozess_Zuweisung"
            Begin Extent = 
               Top = 120
               Left = 38
               Bottom = 250
               Right = 263
            End
            DisplayFlags = 280
            TopColumn = 0
         End
         Begin Table = "USEAP_RPA_Standort"
            Begin Extent = 
               Top = 252
               Left = 38
               Bottom = 365
               Right = 217
            End
            DisplayFlags = 280
            TopColumn = 0
         End
         Begin Table = "USEAP_RPA_Prozesse"
            Begin Extent = 
               Top = 252
               Left = 255
               Bottom = 348
               Right = 434
            End
            DisplayFlags = 280
            TopColumn = 0
         End
      End
   End
   Begin SQLPane = 
   End
   Begin DataPane = 
      Begin ParameterDefaults = ""
      End
   End
   Begin CriteriaPane = 
      Begin ColumnWidths = 11
         Column = 1440
         Alias = 900
         Table = 1170
         Output = 720
         Append = 1400
         NewValue = 1170
         SortType = 1350
         SortOrder = 1410
         GroupBy = 1350
         Filter = 1350
         Or = 1350
         Or = 1350
         Or = 1350
      End
   End
End
' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'VIEW',@level1name=N'USEAP_RPA_ViewProzessUebersicht'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_DiagramPaneCount', @value=1 , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'VIEW',@level1name=N'USEAP_RPA_ViewProzessUebersicht'
GO
USE [master]
GO
ALTER DATABASE [Serviscope] SET  READ_WRITE 
GO
